<?php

    return [

        "report_config_for_non_tabular_report" => [
            "id" => null,
            "name" => "helpdesk-in-depth-new",
            "description" => "test description",
            "is_default" => 1,
            "type" => "helpdesk-in-depth",
            "is_public"=>1,
            "filters" => [
                [
                    "id" => 1,
                    "key" => "created-at",
                    "value" => "last::60~day",
                ]
            ],
            "sub_reports" => [
                [
                    "id" => 1,
                    "report_id" => 1,
                    "identifier" => "helpdesk-in-depth-graph",
                    "data_type" => "category-chart",
                    "selected_chart_type" => "pie",
                    "selected_view_by" => "status",
                    "add_custom_column_url" => "test/url",
                    "columns" => [
                        [
                            "id"=> 1,
                            "key"=> "ticket_number",
                            "label"=> "Ticket Number",
                            "is_visible"=> 1,
                            "is_sortable"=> 1,
                            "is_timestamp"=> 0,
                            "timestamp_format"=> null,
                            "is_html"=> 1,
                            "is_custom"=> 0,
                            "equation"=> "",
                            "order"=> 0,
                            "sub_report_id"=> 4,
                        ]
                    ],
                ]
            ]
        ],

        "report_config_for_tabular_report" => [
            "id" => null,
            "name" => "management-report-new",
            "description" => "test description",
            "is_default" => 1,
            "type" => "management-report",
            "filters" => [
                [
                    "id" => 1,
                    "key" => "created-at",
                    "value" => "last::60~day",
                ]
            ],
            "sub_reports" => [
                [
                    "id" => 1,
                    "report_id" => 1,
                    "identifier" => "management-report-table",
                    "data_type" => "datatable",
                    "selected_chart_type" => null,
                    "selected_view_by" => null,
                    "add_custom_column_url" => "test/url",
                    "columns" => [
                        [
                            "id"=> 1,
                            "key"=> "ticket_number",
                            "label"=> "Ticket Number",
                            "is_visible"=> 1,
                            "is_sortable"=> 1,
                            "is_timestamp"=> 0,
                            "timestamp_format"=> null,
                            "is_html"=> 1,
                            "is_custom"=> 0,
                            "equation"=> "",
                            "order"=> 0,
                            "sub_report_id"=> 4,
                        ]
                    ],
                ]
            ],
        ]
    ];