<?php

namespace App\Plugins\Twitter\Controllers;

use App\Plugins\Twitter\Model\TwitterSystemUser;
use App\Plugins\Twitter\Traits\CommonTwitter;
use Illuminate\Support\Str;
use Logger;
use Illuminate\Http\Request;
use App\Model\MailJob\Condition;
use App\Http\Controllers\Controller;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Requests\TwitterAppRequest;
use App\Plugins\Twitter\Model\TwitterHashtags;

class TwitterController extends Controller
{
    use CommonTwitter;

    public function __construct()
    {
        $this->middleware('role.admin');
    }

    /**
     * Checks whether the twitter keys and secrets are valid
     * @param $settings
     * @return mixed
     */
    private function verifyCredentials($settings)
    {
        $this->TwitterInit($settings);

        $authObject =  $this->twitter->get("account/verify_credentials");

        return $this->convertNestedObjectToArray($authObject);
    }

    private function persistSystemTwitterUser($response)
    {
        if (empty($response['id_str'])) {
            throw new \Exception(trans('Twitter::lang.twitter_cannot_persist_user'));
        }

        TwitterSystemUser::query()->truncate();

        $twitterSysUser = TwitterSystemUser::create([
            'user_id' => $response['id_str'],
            'user_name' => $response['name'],
            'screen_name' => $response['screen_name']
        ]);

        if (!$twitterSysUser) {
            throw new \Exception(trans('Twitter::lang.twitter_cannot_persist_user'));
        }
    }

    /**
     * Perform necessary operations after create/update.
     * @param TwitterApp $app
     * @param $hashTags
     * @param $cronBit
     * @param $twitterAuthData
     * @param bool $updating
     * @return \Illuminate\Http\JsonResponse
     */
    private function afterPersist(TwitterApp $app, $hashTags, $cronBit, $twitterAuthData, $updating = false)
    {
        $hashtagArray = [];

        foreach ($hashTags as $hashtag) {
            array_push($hashtagArray, ['app_id' => $app->id, 'hashtag' => $hashtag]);
        }

        try {
            ($updating) ?  $this->updateHashTags($hashtagArray, $app->id) : $this->fillHashTags($hashtagArray);
            $this->persistSystemTwitterUser($twitterAuthData);
            $this->changeCondition($cronBit);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }

        return successResponse(trans('Twitter::lang.twitter_app_saved'));
    }

    private function prepareDataBeforePersist(TwitterAppRequest $request)
    {
        $cronBit = $request->cron_confirm;

        $hashTags = $request->hashtag_text;

        $userSubmittedData = array_map(function ($item) {
            return trim($item);
        }, $request->only((new TwitterApp())->getFillable()));

        return [
            'hashtags' => $hashTags,
            'cron_bit'  => $cronBit,
            'auth_data' => [
                'access_token' => $userSubmittedData['access_token'],
                'access_secret' => $userSubmittedData['access_token_secret'],
                'consumer_key' => $userSubmittedData['consumer_api_key'],
                'consumer_secret' => $userSubmittedData['consumer_api_secret']
            ],
            'data_to_persist' => $userSubmittedData
        ];
    }

    /**
     * Registers the twitter application
     * @param TwitterAppRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createApp(TwitterAppRequest $request)
    {
        $preparedData = $this->prepareDataBeforePersist($request);

        $twitterAuthData = $this->verifyCredentials($preparedData['auth_data']);

        if (array_key_exists("errors", $twitterAuthData)) {
            return errorResponse($twitterAuthData['errors'][0]['message']);
        } else {
            $app = TwitterApp::create($preparedData['data_to_persist']);

            return ($app)
                ? $this->afterPersist($app, $preparedData['hashtags'], $preparedData['cron_bit'], $twitterAuthData)
                : errorResponse(trans('Twitter::lang.create_error'));
        }
    }

    /**
     * Persists the hashtags associated with the app
     * @param $hashtagArray
     */
    private function fillHashTags($hashtagArray)
    {
        TwitterHashtags::insert($hashtagArray);
    }

    /**
     * Deletes the twitter app
     * @param integer id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteApp($id)
    {
        $twitter = TwitterApp::findOrFail($id);
        try {
            $twitter->hashtags()->delete();
            $deleted = $twitter->delete();
            TwitterSystemUser::query()->truncate();
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }

        return ($deleted)
            ? successResponse(trans('Twitter::lang.deleted-app'))
            : errorResponse(trans('Twitter::lang.not_deleted'));
    }

    /**
     * Changes the cron settings for Twitter plugin
     * @param $enable
     */
    private function changeCondition($enable)
    {
        $condition = Condition::where('job', 'twitter')->first();
        $condition->active = $enable;
        $condition->save();
    }

    /**
     * Return the twitter App for Datatable
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTwitterApp(Request $request)
    {
        $query = TwitterApp::with('hashtags');

        $query->when((bool)($request->ids), function ($q) use ($request) {
            return $q->whereIn('id', $request->ids);
        });

        $query->when((bool)($request->search_query), function ($q) use ($request) {
            $q->where(function ($q) use ($request) {
                return $q->where('app_id', 'LIKE', "%$request->search_query%")
                ->orWhere('secret', 'LIKE', "%$request->search_query%");
            });
        });

        $apps = $query->orderBy((($request->sort_field) ? : 'updated_at'), (($request->sort_order) ? : 'asc'))
        ->paginate((($request->limit) ? : '10'));

        $apps->getCollection()->transform(function ($entity) {
            $entity->hashtags->transform(function ($item) {
                return [
                    "id" => $item->id,
                    "name" => (Str::startsWith($item->hashtag, '#')) ? substr($item->hashtag, 1) : $item->hashtag
                ];
            });
            $entity->cron = Condition::where('job', 'twitter')->value('active');
            return $entity;
        });

        return successResponse('', $apps);
    }

    /**
     * Registers the twitter application
     * @param TwitterAppRequest $request
     * @param $id ID of the twitter app being updated
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateApp(TwitterAppRequest $request, $id)
    {
        $preparedData = $this->prepareDataBeforePersist($request);

        $twitterAuthData = $this->verifyCredentials($preparedData['auth_data']);

        if (array_key_exists("errors", $twitterAuthData)) {
            return errorResponse($twitterAuthData['errors'][0]['message']);
        } else {
            $app = tap(TwitterApp::findOrFail($id))->update($preparedData['data_to_persist']);

            return ($app)
                ? $this->afterPersist($app, $preparedData['hashtags'], $preparedData['cron_bit'], $twitterAuthData, $updating = true)
                : errorResponse(trans('Twitter::lang.update_err'));
        }
    }

    /**
     * Updates the hashtags when app is updated
     * @param $hashtagArray
     * @param $id
     */
    private function updateHashTags($hashtagArray, $id)
    {
        TwitterHashtags::where('app_id', $id)->delete();
        $this->fillHashTags($hashtagArray);
    }

    /**
     * Returns Settings View For Twitter Plugin
     * @return Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsView()
    {
        return view('twitter::settings');
    }
}
