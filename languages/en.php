<?php

/**
 * English localization
 *
 * Check out http://learn.elgg.org/en/stable/guides/i18n.html for details
 */

return [

    'membership:title' => 'Membership management',
    'membership:overview:tabs:reports' => 'Reports',
    'membership:overview:tabs:seasons' => 'Seasons',

    'membership:seasons' => 'Seasons',
    'membership:season:add' => 'Add season',
    'membership:season:edit' => 'Edit season',

    'membership:season:title' => 'Season %s',

    'membership:no_seasons' => 'No seasons have been added',

    'membership:season:form:year:label' => 'Season year',
    'membership:season:form:year:help' => 'Type in the year of the season',
    'membership:season:form:lockdate:label' => 'Lock date',
    'membership:season:form:lockdate:help' => 'Enter the date after which members are not allowed to participate anymore and will be locked',
    'membership:season:form:enddate:label' => 'End date',
    'membership:season:form:enddate:help' => 'Enter the date when the season ends and members will be unlocked',
    'membership:season:form:participationtypes:label' => 'Participation types',
    'membership:season:form:participationtypes:help' => 'Participation types for departments in this season (one type per line in the form of key:label)',

    'membership:productions' => 'Productions',
    'membership:productions:all' => 'All productions',
    'membership:season:no_productions' => 'No production added to the season yet',
    'membership:season:production:add' => 'Add production',

    'membership:production:form:label' => 'Title',
    'membership:production:form:help' => 'Title of the production',
    'membership:production:form:participationtypes:label' => 'Participation types',
    'membership:production:form:participationtypes:help' => 'Participation types for this production (one type per line in the form of key:label)',

    'membership:departments' => 'Departments',
    'membership:departments:title' => 'Departments',

    'membership:settings:departments:participations:label' => 'Department participation types',
    'membership:settings:departments:participations:help' => 'Default participation types for departments in a new season (one type per line in the form of key:label)',
    'membership:settings:production:participations:label' => 'Production participation types',
    'membership:settings:production:participations:help' => 'Default participation types for new productions in a season (one type per line in the form of key:label)',
    'membership:settings:acl:label' => 'Access to membership reports',
    'membership:settings:acl:help' => 'Rules to grant access to the membership reports. See ReportGatekeeper code',

    'membership:errors:wrongParticipationTypes' => 'The provided list of participation types is not valid. Please ensure a value of one type per line in the form of key:label',

    'membership:participations:button' => 'Participations',
    'membership:participations:title' => 'Participations',

    'membership:participations:departments' => 'Participation in departments',
    'membership:participations:productions' => 'Participation in productions',
    'membership:participations:none' => 'No participations',

    'membership:participations:participate' => 'Participate',

    'membership:participations:season:title' => 'Participate in Season %s',

    'membership:participations:saved' => 'Participations saved',

    'membership:reports:profileFields:name' => 'Name',
    'membership:reports:profileFields:displayname' => 'Display name',
    'membership:reports:profileFields:givenname' => 'Given name',
    'membership:reports:profileFields:username' => 'Username',
    'membership:reports:profileFields:email' => 'E-Mail',

    'membership:reports:export:csv' => 'Export',

    'membership:reports:members' => 'Members',
    'membership:reports:title' => 'Report',
    'membership:reports:gatekeeper:error' => 'Your user does not have access to this report',

    'membership:reports:completeseason' => 'Show participations for complete season',

    'membership:reports:profileFields:street' => 'Street',
    'membership:reports:profileFields:zip' => 'ZIP',
    'membership:reports:profileFields:city' => 'City',
    'membership:reports:profileFields:telephone' => 'Telephone',
    'membership:reports:profileFields:mobile' => 'Mobile',

    'membership:season:batch' => 'Batch-Entry',
    'membership:season:batch:title' => 'Batch-Entry for Season %s',
    'membership:season:batch:member' => 'Member',
    'membership:season:batch:save' => 'Save',
    'membership:season:batch:add' => 'Add line',

    'membership:profile:member_since:label' => 'Member since',
    'membership:profile:away_years:label' => 'Years away',
    'membership:profile:title' => 'Membership',

    'membership:membercard' => 'Membership card',
    'membership:membercard:notfound' => 'Member with username %s not found',
    'membership:membercard:noseason' => 'No season found',
    'membership:membercard:participation:invalid' => 'The member currently does not participate in the current season',
    'membership:membercard:participation:valid' => 'The membership is valid',
    'membership:membercard:forbidden' => 'Viewing another members membership card is forbidden',
];
