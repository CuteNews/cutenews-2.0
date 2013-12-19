<div>Do you confirm next action:</div>

<form action="{$PHP_SELF}" method="POST">

    {foreach from=post}<input type="hidden" name="{$post.name}" value="{$post.var}" />{/foreach}
    <input type="hidden" name="__post_data" value="{$post_data}" />

    <!-- there is another POST-data -->
    <div style="border: 1px dashed black; padding: 8px; background: #fffff0; margin: 8px 0; font-weight: bold;">{$text}</div>

    <p>

        <button name="__my_confirm" value="_confirmed">Accept</button>
        <button name="__my_confirm" value="_decline" style="background: #f0e0e0; color: black;"><b>Decline</b></button>

    </p>

</form>