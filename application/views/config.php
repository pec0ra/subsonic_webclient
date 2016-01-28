<h2 id="config-title">Config</h2>
				<div class="text-center alert alert-danger hidden" role="alert" id="error"></div>
				<div class="text-center alert alert-material-teal-300 hidden" role="alert" id="info">Checking connection</div>

				<form id="config-form" action="#" method="POST" accept-charset="utf-8">
					<div class="form-group">
						<label for="server" class=" control-label">Server</label>
    						<div class="">
							<input type="text" class="form-control" name="server" id="server" placeholder="https://<your_server>.com" <?php
						if($this->session->server)
							echo 'value="' . $this->session->server . '"';
?>>
						</div>
  					</div>
					<div class="form-group">
						<label for="pseudo" class=" control-label">Pseudo</label>
    						<div class="">
   							<input type="text" class="form-control" name="pseudo" id="pseudo" placeholder="Enter pseudo" <?php
						if($this->session->pseudo)
							echo 'value="' . $this->session->pseudo . '"';
?>>
						</div>
  					</div>
  					<div class="form-group">
						<label for="password" class=" control-label">Password</label>
    						<div class="">
    							<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						</div>
  					</div>
  					<div class="form-group">
						<label for="bitrate" class="control-label">Bitrate</label>
    						<div class="">
							<select class="form-control" id="bitrate" name="bitrate">
                    						<option value="32">32 kbps</option>
                    						<option value="64">64 kbps</option>
                    						<option value="80">80 kbps</option>
                    						<option value="96">96 kbps</option>
                    						<option value="112">112 kbps</option>
                    						<option value="128">128 kbps</option>
                    						<option value="160">160 kbps</option>
                    						<option value="192">192 kbps</option>
                    						<option value="256">256 kbps</option>
                    						<option value="320">320 kbps</option>
                    						<option value="0" selected>Unlimited</option>
                					</select>
						</div>
  					</div>
					<div class="form-group">
						<div class="checkbox checkbox-primary">
							<label>
      								<input id="remember" name="remember" type="checkbox" checked value="1"> Remember settings
							</label>
						</div>
					</div>
					<div class="form-group">
					<div class="">
  						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
					</div>
				</form>
