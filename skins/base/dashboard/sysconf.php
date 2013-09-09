<?php

$odd = 0;
list($sub, $options, $options_list) = _GL('sub, options, options_list');

cn_snippet_messages();
cn_snippet_bc();

?>
<ul class="sysconf_top">
    <?php foreach ($options_list as $ID => $ol) { ?>
        <li<?php if ($sub == $ID) echo ' class="selected"'; ?>><a href="<?php echo cn_url_modify("sub=$ID") ;?>"><?php echo ucfirst($ID); ?></a></li>
    <?php } ?>
</ul>
<div style="clear: both;"></div>

<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt, sub'); ?>

    <table class="std-table opt_table">
        <tr><th>Option name</th> <th>Configuration parameter</th></tr>

    <?php foreach ($options as $opt_id => $opt_vars)
    {
        if ($opt_vars[0] == 'title')
        {
            echo '<tr class="o_heading"><td colspan="2">'.$opt_vars['title'].'</td></tr>';
            continue;
        }
        ?>
        <tr<?php if ($odd++%2) echo ' style="background: #f8f8f8;"'; ?>>
            <td>
                <div class="o_title"><b><?php echo $opt_vars['title']; ?></b> <?php if ($opt_vars['help']) echo '<a href="#" title="'.cn_htmlspecialchars($opt_vars['help']).'" onclick="return(tiny_msg(this));"><sup>?</sup></a>'; ?></div>
                <div class="o_desc"><?php echo $opt_vars['desc']; ?></div>
            </td>
            <td align="center"><?php

            if ($opt_vars[0] == 'label') echo cn_htmlspecialchars($opt_vars['var']);
            elseif ($opt_vars[0] == 'text') echo '<input type="text" name="config['.$opt_id.']" style="width: 400px;" value="'.cn_htmlspecialchars($opt_vars['var']).'"/>';
            elseif ($opt_vars[0] == 'int')  echo '<input type="text" name="config['.$opt_id.']" size="8" value="'.intval($opt_vars['var']).'"/>';
            elseif ($opt_vars[0] == 'Y/N') echo '<input type="checkbox" name="config['.$opt_id.']" '.($opt_vars['var']?'checked="checked"' : '').' value="Y"/>';
            elseif ($opt_vars[0] == 'select')
            {
                echo '<select name="config['.$opt_id.']"/>';
                foreach ($opt_vars[2] as $_id => $_var)
                    echo '<option value="'.cn_htmlspecialchars($_id).'" '.($_id == $opt_vars['var']? 'selected="selected"':'').'>'.cn_htmlspecialchars($_var).'</option>';

                echo '</select>';
            }
        ?>
            </td>
        </tr>
    <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <td align="center"><input type="submit" style="font-weight:bold;font-size:120%;" value="Save changes" /></td>
        </tr>
    </table>
</form>