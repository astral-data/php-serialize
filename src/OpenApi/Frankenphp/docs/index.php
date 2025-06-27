<?php
require_once dirname(__DIR__, 7) . '/vendor/autoload.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Docs</title>
    <!-- Embed elements Elements via Web Component -->
    <script src="./web-components.min.js"></script>
    <link rel="stylesheet" href="./styles.min.css">
</head>
<body>

<elements-api
    apiDescriptionUrl="<?php echo \Astral\Serialize\OpenApi\Handler\Config::get('doc_url','http://127.0.0.1:8089'); ?>"
    router="hash"
    layout="sidebar"
/>

</body>
</html>
