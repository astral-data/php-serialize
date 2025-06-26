<?php
require_once dirname(__DIR__, 7) . '/vendor/autoload.php';
?>
<redoc spec-url="<?php echo \Astral\Serialize\OpenApi\Handler\Config::get('doc_url','http://127.0.0.1:8089'); ?>"></redoc>
<script src="./redoc.standalone.js"> </script>
