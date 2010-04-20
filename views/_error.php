<?php

    $e =& Application::getInstance()->getCaughtException();

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
<link href="traveller-in-rdf.php" rel="meta" type="application/rdf+xml" title="Metadata RDF o cestovateli" />
</head>
<body>

<h1>Nastala chyba!</h1>
<h2>Zpráva</h2>
<p><?php echo($e); ?></p>
<pre><?php echo($e->getTraceAsString()); ?></pre>

<p xml:lang="en">Powered by <a href="http://raillog.php5.cz/" title="Cestovní železniční zápisník" type="application/xhtml+xml" hreflang="cs">RailLog</a> written by <a href="mailto:jspetrak@gmail.com">Josef Petrák</p>

</body>
</html>
