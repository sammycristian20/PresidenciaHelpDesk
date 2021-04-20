<?php
            Breadcrumbs::register('helpdesk.location.index', function ($breadcrumbs) {
                $breadcrumbs->parent('setting');
                $breadcrumbs->push(Lang::get('lang.location'), route('helpdesk.location.index'));
            });

             Breadcrumbs::register('helpdesk.location.create', function ($breadcrumbs) {
                $breadcrumbs->parent('setting');
                  $breadcrumbs->push(Lang::get('lang.location'), route('helpdesk.location.index'));
                $breadcrumbs->push(Lang::get('lang.create_location'), route('helpdesk.location.create'));

            });

         

              // Breadcrumbs::register('helpdesk.location.edit', function ($breadcrumbs) {
              // $breadcrumbs->parent('setting');
              // $breadcrumbs->push(Lang::get('lang.location'), route('helpdesk.location.index'));
              // $breadcrumbs->push(Lang::get('lang.edit'), url('helpdesk/location-types/{id}/edit'));
              // });
