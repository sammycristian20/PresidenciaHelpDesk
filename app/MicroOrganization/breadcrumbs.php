<?php

  Breadcrumbs::register('helpdesk.micro.organizarion', function ($breadcrumbs) {
                $breadcrumbs->parent('setting');
                $breadcrumbs->push(Lang::get('lang.micro_organization'), route('helpdesk.micro.organizarion'));
            });



         

              Breadcrumbs::register('helpdesk.location.edit', function ($breadcrumbs) {
              $breadcrumbs->parent('setting');
              $breadcrumbs->push(Lang::get('lang.location'), route('helpdesk.location.index'));
              $breadcrumbs->push(Lang::get('lang.edit'), url('helpdesk/location-types/{id}/edit'));
              });