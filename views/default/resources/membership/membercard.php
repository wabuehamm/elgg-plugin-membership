<?php

use Endroid\QrCode\QrCode;
use Wabue\Membership\Entities\Season;
use Wabue\Membership\Tools;

function showError($message)
{
    echo elgg_view(
        'page/error',
        [
            'body' => elgg_view_message('error', $message)
        ]
    );
    exit();
}

$username = elgg_extract('username', $vars, null);

Tools::assert(!is_null($username));

/** @var ElggUser $member */
$member = get_user_by_username($username);

if ($member == false) {
    showError(elgg_echo('membership:membercard:notfound'));
}

$options = [
    'type' => 'object',
    'subtype' => 'season',
    'order_by_metadata' => [
        'name' => 'year',
        'direction' => 'DESC',
        'as' => 'integer',
    ],
    'limit' => 2
];
$currentSeasons = elgg_get_entities($options);

if (count($currentSeasons) < 2) {
    showError(elgg_echo('membership:membercard:noseason'));
}

/** @var Season $currentSeason */
$currentSeason = $currentSeasons[0];

if ($currentSeason->getLockdate() > time()) {
    $currentSeason = $currentSeasons[1];
}

if (!$currentSeason->didMemberParticipate($member)) {
    showError(elgg_echo('membership:membercard:participation:invalid'));
}

if (!elgg_is_logged_in() || (elgg_get_logged_in_user_guid() != $member->getGUID() && !elgg_is_admin_logged_in())) {
    echo elgg_view(
        'page/error',
        [
            'body' => elgg_view_message('success', elgg_echo('membership:membercard:participation:valid'))
        ]
    );
    return;
}

$year = $currentSeason->getYear();
$url = elgg_generate_url('view:user:membercard', [
    'username' => $member->username
]);
$qrcode = new QrCode($url);
$qrcode->setWriterByExtension('svg');

?>
<div id="membercard">
    <div id="icon"><img src="<?php echo $member->getIconURL("large"); ?>"
                        alt="<?php echo $member->getDisplayName(); ?>"></img></div>
    <div id="right">
        <div id="title">Mitgliedsausweis</div>
        <div id="company">
            Westfälische Freilichtspiele e.V.<br/>
            Waldbühne Heessen
        </div>
        <div id="name"><?php echo $member->getDisplayName(); ?></div>
        <div id="footer">
            <div id="qr">
                <?php echo $qrcode->writeString(); ?>
            </div>
            <div id="year">gültig in der Spielzeit <?php echo $year; ?></div>
        </div>
    </div>
</div>
<style>
    @import url('https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&display=swap');

    @media print {
        div#membercard {
            width: 8cm;
            height: 5cm;
            grid-template-rows: 5cm;
        }

        div#icon img {
            width: 3.4cm;
            height: 4.5cm;
        }

        body {
            font-size: 5px;
        }
    }

    @media screen {
        div#membercard {
            max-width: 63em;
            max-height: 37em;
            grid-template-rows: 37em;
        }

        div#icon img {
            width: 26em;
            height: 35em;
        }

        body {
            font-size: 1em;
        }
    }

    div#membercard {
        display: grid;
        grid-template-columns: 50% auto;
        grid-gap: 1px;
        border: 1px solid black;
        margin: 1em 1em auto 1em;
    }

    div#icon {
        grid-column: 1;
        grid-row: 1;
        background-color: white;
        align-self: center;
        justify-self: center;
    }

    div#right {
        grid-column: 2;
        grid-row: 1;
        background-color: white;
        background-image: url(<?php echo elgg_get_simplecache_url('graphics/membership/membercard_logo.png'); ?>);
        background-repeat: no-repeat;
        background-position: bottom right;
        background-size: 17em;
        display: grid;
        grid-template-columns: auto;
        grid-template-rows: 4.5em 3em auto 6em;
        text-align: center;
        padding: 1em;
    }

    div#title {
        grid-column: 1;
        grid-row: 1;
        font-size: 3em;
        font-weight: bold;
    }

    div#company {
        grid-column: 1;
        grid-row: 2;
        font-size: 1em;
        font-weight: lighter;
    }

    div#name {
        grid-column: 1;
        grid-row: 3;
        font-size: 2em;
        font-weight: bold;
        align-self: center;
    }

    div#footer {
        grid-column: 1;
        grid-row: 4;
        display: grid;
        grid-template-columns: auto auto;
        grid-template-rows: auto;
        text-align: center;
        align-self: self-end;
    }

    div#qr {
        grid-column: 1;
        grid-row: 1;
        text-align: left;
    }

    div#qr svg {
        width: 5em;
        height: 5em;
    }

    div#year {
        grid-column: 2;
        grid-row: 1;
        font-size: 2em;
        font-weight: bold;
        text-align: right;
        align-self: self-end;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Open Sans', sans-serif;
        line-height: 1.5;
    }
</style>
