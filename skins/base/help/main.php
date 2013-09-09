<?php
list($help_sections) = _GL('help_sections');

foreach ($help_sections as $id => $section) {
?>
    <?php echo $section; ?>
    <div style="clear:both;"> </div>

<?php } ?>