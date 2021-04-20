export const reportResponse = {
	"success": true,
	"data": {
		"current_page": 1,
		"from": 1,
		"last_page": 1,
		"per_page": 10,
		"total": 2,
		"tickets": [{
			"id": 10429,
			"title": "TEST_TITLE",
			"updated_at": "2018-03-20 07:46:37",
			"status": {
				"id": 1,
				"name": "Open"
			},
			"ticket_number": "AAA-TICKET-NUMBER",
			"source": {
				"id": 1,
				"name": "Web",
				"css_class": "fa fa-internet-explorer"
			},
			"duedate": "2018-03-20 12:46:37",
			"is_overdue": "false",
			"due_today": "true",
			"isanswered": "0",
			"thread_count": 1,
			"collaborator_count": null,
			"attachment_count": 0,
			"poster": "",
			"assigned": {
				"id": 1,
				"user_name": "demo_admin",
				"first_name": "Demo",
				"last_name": "Admin"
			},
			"priority": {
				"id": 1,
				"name": "Low",
				"priority_color": "#00a65a"
			},
			"collaborator_count_relation": [],
			"from": {
				"id": 1,
				"user_name": "demo_admin",
				"first_name": "Demo",
				"last_name": "Admin",
				"profile_pic": "http:\/\/localhost:8000\/lb-faveo\/media\/images\/system.png",
				"email": "Not available"
			}
		}]
	}
};

export const reportColumnResponse = {
   "success": true,
   "data": [
			 {key: "column_key_one", label: "column Label"}
		 ]
};

export const shortCodesResponse = {
	"success":true,
	"data":[
		":created_at",
		":updated_at",
		"@last_status_change(statusName)",
		":duedate",
		":first_response_time",
		":closed_at"
	]
}
