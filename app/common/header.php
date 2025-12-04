<?php
if (!defined('BASE_URL')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="CKU Scan Application - Modern UI with Tailwind CSS and shadcn/ui">
    <title><?= $title ?? "CKU SCAN-NO-BON" ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo url('includes/img/logo-soode.png'); ?>">
    <!-- Tailwind CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/output.css'); ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="<?php echo url('includes/css/bootstrap-icons/bootstrap-icons.css'); ?>"
     <!-- Essential javascripts for application to work-->
    <script src="<?php echo url('includes/js/jquery-3.7.0.min.js'); ?>"></script>
  </head>