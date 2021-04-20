<?php

namespace database\seeds\v_2_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormFieldOption;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Model\helpdesk\Form\CustomFormValue;

class FormSeeder extends Seeder
{

    private $sortOrder;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // FormCategory::truncate();
        // FormField::truncate();
        // FormFieldOption::truncate();
        // FormFieldLabel::truncate();
        // CustomFormValue::truncate();

        //ticket form
        $this->ticketFormSeed();

        //organisation form
        $this->OrganisationFormSeed();

        //use form
        $this->userFormSeed();
    }

    private function getLabelObject($label, $meantFor)
    {
        $labelObject = [];
        $labelObject['language'] = 'en';
        $labelObject['label'] = $label;
        $labelObject['meant_for'] = $meantFor;
        $object = new FormFieldLabel($labelObject);
        return $object;
    }

    /**
     * NOTE: this method uses large number of parameters and this approach should not be used until it is very much required
     * REASON FOR HAVING LARGE NUMBER OF PARAMETERS : so we can know just looking the the documentation that which field is
     * required in which order
     * @param string $category
     * @param string $title
     * @param string $type
     * @param boolean $displayForUser
     * @param boolean $requiredForUser
     * @param boolean $displayForAgent
     * @param boolean $requiredForAgent
     * @param boolean $mediaOption
     * @param boolean $isLinked
     * creates form data based on the parameters passed
     */
    private function createForm($category, $title, $type, $displayForUser, $requiredForUser, $displayForAgent = true,
        $requiredForAgent = true, $mediaOption = false, $isLinked = false, $isEditVisible = true,
        $isLocked = false, $isActive = true)
    {
        $formCategory = FormCategory::updateOrCreate(['category' => $category]);
        $form = [];
        $form['title'] = $title;
        $form['type'] = $type;
        $form['display_for_agent'] = $displayForAgent;
        $form['display_for_user'] = $displayForUser;
        $form['required_for_agent'] = $requiredForAgent;
        $form['required_for_user'] = $requiredForUser;
        $form['default'] = true;
        $form['is_edit_visible'] = $isEditVisible;
        $form['is_active'] = $isActive;
        $form['is_linked'] = $isLinked;
        $form['media_option'] = $mediaOption;
        $form['sort_order'] = ++$this->sortOrder;

        $formField = new FormField($form);
        $RequesterLabelForAgent = $this->getLabelObject($title, 'agent');
        $RequesterLabelForUser = $this->getLabelObject($title, 'user');
        $formFieldObject = $formCategory->formFields()->save($formField);
        $formFieldObject->labels()->saveMany([$RequesterLabelForAgent, $RequesterLabelForUser]);
    }


    private function ticketFormSeed()
    {
        $this->sortOrder = 0;
        $this->createForm('ticket', 'Requester', 'text', true, true, true, true, false, false, false, false);
        $this->createForm('ticket', 'CC', 'select', false, false, true, false, false, false, false);
        $this->createForm('ticket', 'Subject', 'text', true, true);
        $this->createForm('ticket', 'Status', 'select', false, false, true, false, false, false, false);
        $this->createForm('ticket', 'Priority', 'select', true, true);
        $this->createForm('ticket', 'Location', 'select', false, false, true, false);
        $this->createForm('ticket', 'Source', 'select', false, false, true, false);
        $this->createForm('ticket', 'Help Topic', 'multiselect', true, true);
        $this->createForm('ticket', 'Department', 'multiselect', false, false, true, false, false, false, true);
        $this->createForm('ticket', 'Type', 'select', true, false, true, false);
        $this->createForm('ticket', 'Assigned', 'select', false, false, true, false);
        $this->createForm('ticket', 'Description', 'textarea', true, true, true, true, false, false, false);
        $this->createForm('ticket', 'Captcha', 'select', false, false, false, false, false, false, false);
    }

    private function OrganisationFormSeed()
    {
        $this->sortOrder = 0;
        $this->createForm('organisation', 'Organisation Name', 'text', true, true);
        $this->createForm('organisation', 'Phone', 'number', true, false);
        $this->createForm('organisation', 'Organisation Domain Name', 'select2', true, false, true, false);
        $this->createForm('organisation', 'Description', 'textarea', true, false, true, false);
        $this->createForm('organisation', 'Address', 'textarea', false, false, true, false);
        $this->createForm('organisation', 'Organisation Logo', 'file', true, false, true, false);
        $this->createForm('organisation', 'Organisation Department', 'select2', false, false, true,
          false, false, false, false, false, false);
        $this->createForm('organisation', 'Captcha', 'select', false, false, false, false, false, false, false);
    }

    private function userFormSeed()
    {
        $this->sortOrder = 0;
        $this->createForm('user', 'First Name', 'text', true, true);
        $this->createForm('user', 'Last Name', 'text', true, true);
        $this->createForm('user', 'User Name', 'text', false, false, true, false);
        $this->createForm('user', 'Work Phone', 'number', false, false, true, false);
        $this->createForm('user', 'Email', 'email', true, true);
        $this->createForm('user', 'Mobile Phone', 'number', false, false, true, false);
        $this->createForm('user', 'Address', 'textarea', false, false, true, false);
        $this->createForm('user', 'Organisation', 'select2', false, false, true, false);
        $this->createForm('user', 'Organisation Department', 'select2', false, false, true,
          false, false, false, false, false, false);
        $this->createForm('user', 'Captcha', 'select', false, false, false, false, false, false, false);
    }
}
