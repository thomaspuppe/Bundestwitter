<section class="content-header">
  <h1>
    PHP Info
  </h1>
  <ol class="breadcrumb">
    <li><a href="/admin/status"><i class="glyphicon glyphicon-dashboard"></i> Status</a></li>
    <li class="active">PHP Info</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

<?php
ob_start();
phpinfo();
$phpinfo = ob_get_clean();

# Body-Content rausholen
$phpinfo = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $phpinfo);
# XHTML-Fehler korrigieren
$phpinfo = str_replace('module_Zend Optimizer', 'module_Zend_Optimizer', $phpinfo);
# <font> durch <span> ersetzen
$phpinfo = str_replace('<font', '<span', $phpinfo);
$phpinfo = str_replace('</font>', '</span>', $phpinfo);
#Table
$phpinfo = str_replace('<table', '<table class="table table-bordered table-striped table-condensed table-hover" style="table-layout: fixed;word-wrap: break-word;"', $phpinfo);
$phpinfo = str_replace('<tr class="h"><th>', '<thead><tr><th>', $phpinfo);
$phpinfo = str_replace('</th></tr>', '</th></tr></thead><tbody>', $phpinfo);
$phpinfo = str_replace('</table>', '</tbody></table>', $phpinfo);
# Schlüsselwörter grün oder rot einfärben
$phpinfo = preg_replace('#>(on|enabled|active)#i', '><span class="text-success">$1</span>', $phpinfo);
$phpinfo = preg_replace('#>(off|disabled)#i', '><span class="text-error">$1</span>', $phpinfo);

echo $phpinfo;
?>

</section>
