<?php
	/**
     *
     * BREAD CRUMBS
     *
     */

    Breadcrumbs::register('ldap.settings.index', function ($breadcrumbs) {
        $breadcrumbs->parent('plugins');
        $breadcrumbs->push(Lang::get('Ldap::lang.ldap_settings'), route('ldap.settings.index'));
    });

    Breadcrumbs::register('ldap.settings.create', function ($breadcrumbs) {
        $breadcrumbs->parent('ldap.settings.index');
        $breadcrumbs->push(Lang::get('lang.create'), route('ldap.settings.create'));
    });

    Breadcrumbs::register('ldap.settings.edit', function ($breadcrumbs, $id) {
        $breadcrumbs->parent('ldap.settings.index');
        $breadcrumbs->push(Lang::get('lang.edit'), route('ldap.settings.edit', ['id'=> $id]));
    });

