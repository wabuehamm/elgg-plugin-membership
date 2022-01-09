# Wabue Membership management plugin

Elgg plugin vor membership management of the Waldbuehne Heessen

# Requirements

* Elgg >= 3.0.0

# Installation

Download a release and unzip the file into the `mods` directory of Elgg.

# Creating a new report

* Add a new department/general or production level report to `views/default/object/elements/seasonReport.php`.
  Make sure to include calls to `$acl->isParticipationAllowed($user, $entity->guid, 0, "_all")` to check if accessing
  the report is allowed for the currently logged-in user
* Use a new ACL tag and create a new route inside `elgg-plugin.php` for the new report
* Use a new i18n tag, place it in `languages/en.php` and translate it in `languages/de.php`
* Place the view for the new route in or under `views/default/resources/membership` and develop the report, optionally
  refined views from `views/default/page/components`, especially `views/default/object/elements/simpleReportTable.php`
  may be useful
* Use `classes/Wabue/Membership/Tools.php` for the business logic to create the report data
* Use the same route for CSV export buttons
* Use the same name for the CSV report in `views/csv/resources/membership`
* Use `views/csv/object/elements/simpleReportTable.php` as a shortcut for simple reports

# Development

## Release

Run the following command to release a new version to GitHub

    GITHUB_TOKEN=<my token> grunt release:<new release number>
