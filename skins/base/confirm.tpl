<section>
	<div class="container">
	<h3>Do you confirm next action:</h3>
    <form action="{$PHP_SELF}" method="POST">

        {foreach from=post}<input type="hidden" name="{$post.name}" value="{$post.var}" />{/foreach}
		<input type="hidden" name="__post_data" value="{$post_data}" />

		<!-- there is another POST-data -->
		<div class="well lead">
            <p>{$text}</p>
			<p>
				<button class="btn btn-danger" name="__my_confirm" value="_confirmed"><i class="fa fa-trash"></i> Accept</button>
				<button class="btn btn-primary" name="__my_confirm" value="_decline" ><b>Decline</b></button>
			</p>
		</div>

    </form>
	</div>
</section>