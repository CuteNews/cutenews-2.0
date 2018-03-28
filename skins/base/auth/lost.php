<style>


.form-recovery {
  max-width: 830px;
  padding: 15px;
  margin: 0 auto;
}
</style>

<section>
	<div class="container">
		<form class="form-recovery" action="<?php echo PHP_SELF; ?>" method="POST">

			<h2>Recovery Password</h2>

					<input type="hidden" name="register" />
					<input type="hidden" name="lostpass" />

		   
					<div class="form-group">
						<label for="user"> User:</label>
						<input id="user" class="form-control"  type="text" name="username"/>
					</div>
				   
				   
					<div class="form-group">
						<label for="email"> Confirm Password:</label>
						<input id="email" class="form-control"  type="text" name="email"/>
					</div>
					
					<div class="form-group">
						<label for="secret"> Confirm Password:</label> Secret word (optional)
						<input id="secret" class="form-control"  type="text" name="secret"/>
					</div>
				   
					<div class="form-group">
					<input class="btn btn-primary" type="submit" value="Send me the Confirmation">
					</div>
		   

		  
			<h3>Tips:</h3>
		   
		   <ul>
				<li>If the username and email match in our users database, and email with furher instructions will be sent to you.</li>
				<li>2. A secret word to protect against unauthorized distribution of letters, as an attacker can get your name and e-mail.
					   A secret word can not contain spaces!</li>
		   </ul>

		</form>
	</div>
</section>