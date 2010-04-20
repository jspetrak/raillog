<?php
  $model =& ApplicationModel::getInstance();
  header('Content-type: text/html;charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Cestovní železniční zápisník</title>
<link href="http://purl.org/DC/elements/1.0/" rel="schema.DC" />
<link href="raillog.css" type="text/css" rel="stylesheet" />
<meta name="DC.Language" content="cs" />
<meta name="DC.Format" content="text/html" />
<meta name="DC.Title" content="Cestovní železniční zápisník" />
<?php if($model->getTraveller()->getHomepage()): ?>
<link href="<?php echo($model->getTraveller()->getHomepage()); ?>" rel="author" type="text/html" />
<?php endif; ?>
</head>
<body>

<h1>Železniční cestovní zápisník</h1>

<p><a href="cars.php">Statistiky využití vozů</a></p>

<form action="index.php" method="GET">
<fieldset>
<legend>Datové soubory</legend>
<select id="dataFileName" name="dataFileName">
<?php foreach (HttpRequest::get()->getDataFileNames() as $dataFileName): ?>
<option <?php if (($dataFileName.'.xml') == HttpRequest::get()->getDataFileName()) { echo('selected="selected"'); } ?> value="<?php echo($dataFileName); ?>"><?php echo($dataFileName); ?></option>
<?php endforeach; ?>
</select>
 <input type="submit" value="Zobrazit" />
</fieldset>
</form>

<fieldset>
<legend>Celkové statistiky</legend>
<p><em>Ujeto celkem</em>: <?php echo($model->getLength()); ?> km</p>
<p><em>Celkový procestovaný čas</em>: <?php echo(Viewer::transformSeconds($model->getDurationInSec())); ?></p>
<p><em>Jízdní řády</em>: 
<?php foreach ($model->getGvds() as $gvd): ?>
<a href="#<?php echo($gvd->getId()); ?>"><?php echo(Viewer::viewDate($gvd->getStart())); ?> - <?php echo(Viewer::viewDate($gvd->getEnd())); ?></a>
<?php endforeach; ?>
</p>
</fieldset>
<?php if (!is_null($model->getTraveller())): ?>
<h2>Informace o uživateli</h2>
<div class="User">
<p><em>Jméno</em>: <?php echo($model->getTraveller()->getName()); ?></p>
<?php if (!is_null($model->getTraveller()->getNick()) || $model->getTraveller()->getNick() != ''): ?>
<p><em>Přezdívka</em>: <?php echo($model->getTraveller()->getNick()); ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getEmail())): ?>
<p><em>E-mail</em>: <?php for ($i = $model->getTraveller()->getEmailAsIterator(); $i->valid();): ?><a href="mailto:<?php echo($i->current()); ?>"><?php echo($i->current()); ?></a><?php $i->next(); if($i->valid()): ?>,<?php endif; ?> <?php endfor; ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getWeblog())): ?>
<p><em>Weblog</em>: <?php for ($i = $model->getTraveller()->getWeblogAsIterator(); $i->valid();): ?><a href="<?php echo($i->current()); ?>"><?php echo($i->current()); ?></a><?php $i->next(); if($i->valid()): ?>,<?php endif; ?> <?php endfor; ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getHomepage())): ?>
<p><em>Homepage</em>: <?php for ($i = $model->getTraveller()->getHomepageAsIterator(); $i->valid();): ?><a href="<?php echo($i->current()); ?>"><?php echo($i->current()); ?></a><?php $i->next(); if($i->valid()): ?>,<?php endif; ?> <?php endfor; ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getJabber())): ?>
<p><em>Jabber</em>: <?php echo(implode(', ', $model->getTraveller()->getJabber())); ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getAim())): ?>
<p><em>AIM</em>: <?php echo(implode(', ', $model->getTraveller()->getAim())); ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getIcq())): ?>
<p><em>ICQ</em>: <?php echo(implode(', ', $model->getTraveller()->getIcq())); ?></p>
<?php endif; ?>
<?php if (!is_null($model->getTraveller()->getMsn())): ?>
<p><em>MSN</em>: <?php echo(implode(', ', $model->getTraveller()->getMsn())); ?></p>
<?php endif; ?>
</div>
<?php endif; ?>

<h2>Statistiky jízdného</h2>
<?php $tickets = $model->getTickets(); if (sizeof($tickets) > 0): ?>
<table summary="Statistiky jízdného">
<thead>
<tr>
<th>Lístek</th><th>Ujeto</th><th>Cena</th><th>Cena / km</th><th>Cena / km (Kč)</th>
</tr>
</thead>
<tbody>
<?php foreach ($tickets as $ticket): ?>
<tr<?php if ($ticket->isArchived()) { echo(' class="archived"'); } ?>>
<td><?php echo($ticket->getName()); ?></td>
<td><?php echo($ticket->getDistance()); ?> km</td>
<td><?php echo($ticket->getPrice()); ?> <?php echo($ticket->getCurrency()); ?></td>
<td><?php if ($ticket->getDistance() > 0) { echo(round(($ticket->getPrice() / $ticket->getDistance()),2)); } else { echo($ticket->getPrice()); } ?> <?php echo($ticket->getCurrency()); ?></td>
<td><?php if ($ticket->getDistance() > 0 && !($ticket->getCurrency()=='Kč')) { echo(round(($ticket->getPrice() * 24.25 / $ticket->getDistance()),2)); echo(' Kč'); } else { echo('&mdash;'); } ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<h2>Statistiky jízd</h2>

<?php foreach ($model->getGvds() as $gvd): ?>
<div class="Gvd" id="<?php echo($gvd->getId()); ?>">
<h3><?php echo(Viewer::viewDate($gvd->getStart())); ?> - <?php echo(Viewer::viewDate($gvd->getEnd())); ?></h3>
<p><em>Najeto</em>: <?php echo($gvd->getLength()); ?> km</p>
<?php if(count($gvd->getAppearedStationsAsMap()) > 0): ?>
<p><em>Projeté stanice</em>: 
<?php for ($i = $gvd->getAppearedStationsAsMapIterator(); $i->hasNext(); $i->next()) { 
  echo($i->current());
  if ($i->isLast()) echo(', ');
} ?>
</p>
<?php endif; ?>
<?php if(count($gvd->getAppearedTrainsAsMap()) > 0): ?>
<p><em>Vlaky, kterými jsem jel</em>: 
<?php for ($i = $gvd->getAppearedTrainsAsMapIterator(); $i->hasNext(); $i->next()) { 
  echo($i->current());
  if ($i->isLast()) echo(', ');
} ?>
</p>
<?php endif; ?>

<?php if(count($gvd->getTrainStatAsList()) > 0): ?>
<table summary="Statistika jízd jednotlivými soupravami">
<thead>
<tr>
<th>Vlak</th>
<th>Počet jízd</th>
</tr>
</thead>
<tbody>
<?php foreach($gvd->getTrainStatAsList() as $item): ?>
<tr>
<td><?php echo($item->getObjectLabel()); /* We now, that it is Train class so ... */ if ($item->getObject()->getNoteRoad() != null && $item->getObject()->getNoteRoad() != '') { ?> <em class="noteRoad">(<?php echo($item->getObject()->getNoteRoad()); ?>)</em><?php } ?></td>
<td><?php echo($item->getCount()); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<?php foreach ($gvd->getJourneys() as $journey): ?>
<div class="Journey<?php if ($journey->isPlanned()): ?> planned<?php endif; ?>">
<h4><?php echo($journey->getStartStation()); ?> &gt; <?php echo($journey->getEndStation()); ?></h4>
<?php
	$journeyParts = $journey->getJourneyParts();

	for ($i = 0; $i < sizeof($journeyParts); $i++):
		$journeyPart = $journeyParts[$i];
?>
<ul>
<li>
<p><cite><?php echo($journeyPart->getTrain()); ?></cite>: <?php echo($journeyPart->getStartStation()); ?> &gt; <?php echo($journeyPart->getEndStation()); ?> (<?php echo(Viewer::viewDateTime($journeyPart->getStart())); ?> &ndash; <?php echo(Viewer::viewDateTime($journeyPart->getEnd())); ?></td>) <?php echo($journeyPart->getLength()); ?> km
<?php if (!is_null($journeyPart->getCar()) || !is_null($journeyPart->getNote()) || !is_null($journeyPart->getLocomotive())): ?>
*</p><p>*)
<?php if (!is_null($journeyPart->getLocomotive())): ?><em>Lokomotiva</em>: <?php echo($journeyPart->getLocomotive()); ?><?php endif; ?> 
<?php if (!is_null($journeyPart->getCar())): ?><em>Vůz</em>: <?php echo($journeyPart->getCar()); ?><?php endif; ?> 
<?php if (!is_null($journeyPart->getNote())): ?><em>Poznámka</em>: <?php echo($journeyPart->getNote()); ?><?php endif; ?>
</p>

</p>
<?php endif; ?>
</li>
</ul>
<?php endfor; ?>
<p><em>Doba jízdy</em>: <?php echo(Viewer::transformSeconds($journey->getDurationInSec())) ?> | <em>Vzdálenost</em>: <?php echo($journey->getLength()); ?> km</p>
</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php if (count($model->getStatJourneysAsList()) > 0): ?>
<table summary="Statistika jízd na jednotlivých trasách">
<thead>
<tr>
<th>Trasa</th>
<th>Počet jízd</th>
</tr>
</thead>
<tbody>
<?php foreach($model->getStatJourneysAsList() as $item): ?>
<tr>
<td><?php echo($item->getAObject()); ?> &gt; <?php echo($item->getBObject()); ?></td>
<td><?php echo($item->getCount()); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<p xml:lang="en">Powered by <a href="http://raillog.php5.cz/" title="Cestovní železniční zápisník" type="application/xhtml+xml" hreflang="cs">RailLog</a> written by <a href="mailto:jspetrak@gmail.com">Josef Petrák</a></p>
<p xml:lang="en">Based on PHP 5, XML, Relax NG, XHTML &amp; CSS</p>

</body>
</html>
