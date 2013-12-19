<?php

list($list) = _GL('list');
list($type, $name, $desc, $meta, $group, $req) = _GL('type, name, desc, meta, group, req');

cn_snippet_messages();
cn_snippet_bc();

?>
<table class="std-table" width="100%">
    <tr> <th>Name</th> <th>Type</th> <th>Desc</th> <th>Meta</th> <th>Group</th> <th>Required</th> </tr>

    <?php if ($list) foreach ($list as $_name => $item) { ?>
        <tr <?php if ($name == $_name) echo 'class="row_selected"'; ?>>
            <td align="center"><a href="<?php echo cn_url_modify("extr_name=$_name"); ?>"><?php echo cn_htmlspecialchars($_name); ?></a></td>
            <td align="center" style="color: #666666;"><?php echo $item['type']; ?></td>
            <td><?php echo cn_htmlspecialchars($item['desc']); ?></td>
            <td><?php echo cn_htmlspecialchars($item['meta']); ?></td>
            <td align="center"><?php echo $item['grp']; ?></td>
            <td align="center"><?php echo $item['req'] ? "YES" : ''; ?></td>
        </tr>
    <?php } else { ?><tr><td colspan="5">No entries found</td></tr><?php } ?>

</table>

<br/>
<form action="<?php echo PHP_SELF; ?>" method="POST">
    <?php cn_form_open('mod, opt'); ?>

    <table class="panel">

        <tr>
            <td align="right">Field type</td>
            <td>
                <select name="type">
                    <option value="text">Text</option>
                    <option value="image" <?php if ($type == 'image') echo 'selected'; ?>>Image/Resource</option>
                    <option value="select" <?php if ($type == 'select') echo 'selected'; ?>>Select</option>
                    <option value="checkbox" <?php if ($type == 'checkbox') echo 'selected'; ?>>Checkbox</option>
                    <option value="price" <?php if ($type == 'price') echo 'selected'; ?>>Price</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">Name <span class="required">*</span></td>
            <td><input type="text" style="width: 250px;" name="name" value="<?php echo cn_htmlspecialchars($name); ?>"/></td>
        </tr>
        <tr>
            <td align="right">Group</td>
            <td style="color: #666;"><input style="width: 250px;" type="text" name="group" value="<?php echo cn_htmlspecialchars($group); ?>"/>
                <br/>Grouping allows you to group fields in different blocks</td>
        </tr>
        <tr>
            <td align="right">Description</td>
            <td><input style="width: 675px;" type="text" name="desc" value="<?php echo cn_htmlspecialchars($desc); ?>"/></td>
        </tr>
        <tr>
            <td align="right">Meta</td>
            <td style="color: #666;"><input style="width: 675px;" type="text" name="meta" value="<?php echo cn_htmlspecialchars($meta); ?>"/>
                <br/>Meta used by SELECT box, enter value1;value2;...;valueN</td>
        </tr>

        <tr>
            <td align="right"><input type="checkbox" name="req" <?php if ($req) echo 'checked'; ?> value="Y"/></td>
            <td>Required</td>
        </tr>

        <tr>
            <td align="right"><input type="checkbox" name="remove" value="Y"/></td>
            <td>Delete field</td>
        </tr>


        <tr><td>&nbsp;</td> <td><input type="submit" value="Add/Replace field" /></td></tr>

    </table>


</form>