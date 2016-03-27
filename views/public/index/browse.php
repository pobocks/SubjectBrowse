<?php
$pageTitle = __('References');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'reference',
));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <ul class='references'>
    <?php foreach ($references as $slug => $label): ?>
        <li><?php echo sprintf('<a href="%s" title="%s">%s (%d)</a>',
            url('references/' . $slug),
            __('Browse %s', $label),
            $label,
            $this->referenceCount($slug)); ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<?php echo foot();
