<?php

list($grp, $group_id, $group_name, $group_grp, $access, $group_add, $group_sub, $group_system, $form_desc) = _GL('grp, group_id, group_name, group_grp, access, group_add, group_sub, group_system, form_desc');

cn_snippet_messages();
cn_snippet_bc();
?>

<!-- show categories -->
<form action="<?php echo PHP_SELF; ?>" method="POST">

    <?php cn_form_open('mod, opt'); ?>
    <input type="hidden" name="group_id" value="<?php echo intval($group_id); ?>" />

    <table class="std-table wide">
        <tr><th>ID</th> <th>Name</th> <th>Access rights</th> <th>In groups</th> <th width="1">Sys</th> </tr>

        <?php foreach ($grp as $id => $acl) { ?>

            <tr<?php if ($id == $group_id) { echo ' class="row_selected"'; } ?>>

                <td align="center"><?php echo $id; ?></td>
                <td><a href="<?php echo cn_url_modify("group_id=$id"); ?>"><?php echo cn_htmlspecialchars($acl['name']); ?></a></td>
                <td><?php

                    $ps = array();
                    $sp = spsep($acl['acl']);

                    foreach ($sp as $name)
                    {
                        $ps[] = '<a href="#" title="'.join('; ', $form_desc[$name]).'" onclick="return(tiny_msg(this));">'.$name.'</a>';
                    }
                    echo join(', ', $ps);

                ?>
                </td>
                <td><?php echo join('<br>', $acl['grp']); ?></td>
                <td align="center"><?php echo $acl['system'] ? 'Y' : ''; ?> </td>

            </tr>

        <?php } ?>

    </table>

    <br/>
    <table class="panel">

        <tr>
            <td align="right"><input type="checkbox" name="master_box" onclick="check_uncheck_all('acl[]');" value="Y"/></td>
            <td>All privileges</td>
        </tr>

        <tr>
            <td valign="top" align="right" style="padding: 8px 0 0 0;">Name <span class="required">*</span></td>
            <td><input style="width: 650px;" type="text" name="group_name" value="<?php echo cn_htmlspecialchars($group_name); ?>"/><div style="color: #888; margin: 0 0 8px 0;">enter another name to create inherited group</div></td>
        </tr>

        <tr>
            <td valign="top" align="right" style="padding: 8px 0 0 0;">Consists</td>
            <td><input style="width: 650px;" type="text" name="group_grp" value="<?php echo cn_htmlspecialchars($group_grp); ?>"/><div style="color: #888; margin: 0 0 8px 0;">group ids, comma separated</div></td>
        </tr>

        <!-- show all ACLs -->
        <?php foreach ($access as $group => $cons) {  ?>
            <tr><td></td><td><hr/></td></tr>
            <tr>
                <td><?php echo $group; ?></td>
                <td>
                    <?php foreach ($cons as $name => $desc) { ?>
                        <span style="float: left; width: 200px;">
                            <input title="<?php echo $name; ?>" type="checkbox" name="acl[]" <?php if ($desc['c']) { echo 'checked'; } ?> value="<?php echo $name; ?>" />
                            <?php
                                if ($desc['t'])
                                {
                                    echo '<a href="#" title="'.cn_htmlspecialchars($desc['t']).'" onclick="return (tiny_msg(this));">'.cn_htmlspecialchars($desc['d']).'</a>';
                                }
                                else
                                {
                                    echo cn_htmlspecialchars($desc['d']);
                                }
                            ?>
                        </span>
                    <?php } ?>
                <td>
            </tr>
        <?php } ?>
        <tr><td></td><td><hr/></td></tr>

            <tr>
                <td>&nbsp;</td>
                <td>
                    <div style="float:left;">
                        <input type="submit" onclick="setMode('add');" value="Add group" />&nbsp;
                        <?php if($group_id&&($group_id!=1&&$group_id!=5)) {?><input type="submit" onclick="setMode('edit');" value="Edit group" />&nbsp;<?php } ?>
                        <input type="hidden" id="mode" name="mode" value=""/>
                        <!-- reset group to def. values -->
                        <?php if ($group_system && $group_id&&($group_id!=1&&$group_id!=5)) { ?>
                            <input type="submit" onclick="resetGroup();" value="Reset this group by default" />
                            <input type="hidden" id="rest_grp" name="reset_group" value=""/>
                        <?php } ?>   
                    </div>
                    <div style="width:100%;text-align:right;">
                        <!-- delete non-system group -->
                       <?php if (!$group_system && $group_id) { ?>                
                           <input type="submit" onclick="deleteGroup();" value="Delete group" />
                           <input type="hidden" id="del_grp" name="delete_group"  value=""/>
                       <?php } ?>                         
                    </div>
                </td>
  
            </tr>

    </table>

</form>
<script type="text/javascript">
    function resetGroup()
    {
        var r=document.getElementById('rest_grp');
        r.setAttribute('value','Y');
        
    }
    
    function deleteGroup()
    {
        var d=document.getElementById('del_grp');
        d.setAttribute('value','Y');
    }
    
    function setMode(md)
    {
        var m=document.getElementById('mode');
        m.setAttribute('value',md);
    }
</script>