<h2>Artists</h2>
<table class="table table-hover">
<?php
foreach($artists->index as $index){
?>
<tr>
	<th><?php echo $index->attributes()->name;?></th>
	<th>Albums</th>
</tr>
<?php
	foreach($index->artist as $artist){
?>
<tr>
	<td><a href="#artist" onclick="loadPage('main', 'artist', <?php echo $artist->attributes()->id;?>, false);return false;"><?php echo $artist->attributes()->name;?></a></td>
	<td class="text-muted"><?php echo $artist->attributes()->albumCount;?></td>
</tr>
<?php
	}
}
?>
</table>
