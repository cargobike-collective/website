<?php

use Sabre\VObject\Component\VCalendar;

/**
 * iCalendar (.ics) representation of an event, built with sabre/vobject.
 * Reachable at {event-url}.ics — linked from the date in event.php.
 *
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Page $page
 */

$tz     = new DateTimeZone('Europe/Berlin');
$utc    = new DateTimeZone('UTC');
$start  = new DateTime($page->date()->value() ?: 'now', $tz);
$allDay = $start->format('His') === '000000';

$addrCity = $page->addressCity()->or($page->city())->value();
$zipCity  = trim($page->zip()->value() . ' ' . $addrCity);
$location = trim(implode(', ', array_filter([$page->street()->value(), $zipCity])));

$host = parse_url($kirby->url(), PHP_URL_HOST) ?: 'cargobike-collective';

$vcalendar = new VCalendar();
$vcalendar->PRODID = '-//Cargobike Collective//Events//DE';
$vcalendar->METHOD = 'PUBLISH';

/** @var \Sabre\VObject\Component\VEvent $vevent */
$vevent = $vcalendar->add('VEVENT', [
  'UID'     => $page->uuid()->id() . '@' . $host,
  'DTSTAMP' => new DateTime('now', $utc),
  'SUMMARY' => $page->title()->value(),
  'URL'     => $page->url(),
]);

if ($allDay === true) {
  // All-day event: date-only DTSTART/DTEND (end is exclusive → next day).
  $vevent->add('DTSTART', $start, ['VALUE' => 'DATE']);
  $vevent->add('DTEND', (clone $start)->modify('+1 day'), ['VALUE' => 'DATE']);
} else {
  // Timed event: store in UTC; default 2-hour duration (no end time in the model).
  $vevent->add('DTSTART', (clone $start)->setTimezone($utc));
  $vevent->add('DTEND', (clone $start)->setTimezone($utc)->modify('+2 hours'));
}

if ($location !== '') {
  $vevent->add('LOCATION', $location);
}
if ($page->text()->isNotEmpty() === true) {
  $vevent->add('DESCRIPTION', trim($page->text()->value()));
}

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $page->slug() . '.ics"');
echo $vcalendar->serialize();
