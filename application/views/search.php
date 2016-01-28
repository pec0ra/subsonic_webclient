<h2>Search</h2>

<form class="form-inline" id="search-form" autocomplete="off">
	<div class="form-group">
		<input type="text" class="form-control search-input floating-label" name="search" id="search" placeholder="Search" value="<?php echo $query;?>">
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary">Submit</button>
	</div>
</form>

<div class="list-group search-results" id="search-results"></div>
