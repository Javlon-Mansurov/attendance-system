<?php
$messages = [
    'created'     => ['success', 'Сабти вақти корӣ бомуваффақият илова карда шуд.'],
    'updated'     => ['success', 'Сабти вақти корӣ навсозӣ шуд.'],
    'deleted'     => ['warning', 'Сабти вақти корӣ нест карда шуд.'],
    'emp_deleted' => ['warning', 'Корманд нест карда шуд.'],
    'fk_blocked'  => ['danger',  'Кормандеро, ки сабтҳои вақти корӣ дорад, нест кардан мумкин нест (маҳдудияти калиди хориҷӣ).'],
];
$msgKey = $_GET['msg'] ?? '';
if (isset($messages[$msgKey])):
    [$type, $text] = $messages[$msgKey];
?>
<div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
    <?= $this->e($text) ?>
</div>
<?php endif; ?>
