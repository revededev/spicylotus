<?php
echo '
<button id="close" type="submit" class="btn">fermer</button>
<p id="id" hidden>'.$user_id.'</p>

<div>
	<ul id="info_user">
		<li  class="form-">'.$img.'</li>
		<li id="name">'.$selected_user['name'].'</li>
		<li id="email">'.$selected_user['email'].'</li>
		<li id="tel">Tel : '.$selected_user['tel'].'</li>
	</ul>
	<ul class="flex-row-wrap">
		<li id="assurance" style="order:'.$order_assurance.'">
			<p>Certificat d\'assurance : <i class="erreur" id="is_assurance">'.$selected_user['is_assurance'].'</i></p>';
			if (isset($selected_user['path_assurance']) && $selected_user['is_assurance'] === 'en attente'){
				echo '
				<div>
					<input type="radio" id="statut_assuranceYes" name="statut_assurance" value="valide">
					<label for="statut_assuranceYes">valide</label>

					<input type="radio" id="statut_assuranceNo" name="statut_assurance" value="non valide">

					<input type="radio" name="statut_assurance" class="hidden" checked hidden value="noChange">
					<label for="statut_assuranceYes">non valide</label>
				</div>
				<div class="docPhoto">
					<img id="path_assurance" src="'.$selected_user['path_assurance'].'" alt="Photo assurance">
				</div>';
			}else{echo '<p class="erreur" hidden>Prévoir d\'afficher/revérifier à la demande.</p>';}
		echo '
		</li>
		<li id="certif" style="order:'.$order_certif.'">
			<p>Certificat médical : <i class="erreur" id="is_certif">'.$selected_user['is_certif'].'</i></p>';
			if (isset($selected_user['path_certif']) && $selected_user['is_certif'] === 'en attente'){
				echo '
				<div>
					<input type="radio" id="statut_certifYes" name="statut_certif" value="valide">
					<label for="statut_certifYes">valide</label>

					<input type="radio" id="statut_certifNo" name="statut_certif" value="non valide">

					<input type="radio" checked name="statut_certif" class="hidden" hidden value="noChange">

					<label for="statut_certifYes">non valide</label>
				</div>
				<div class="docPhoto">
					<img id="path_certif" src="'.$selected_user['path_certif'].'" alt="Photo certificat médical">
				</div>';
			}else{echo '<p class="erreur" hidden>Prévoir d\'afficher/revérifier à la demande.</p>';								}
		echo '
		</li>
		<li id="licence" style="order:'.$order_licence.'">
			<p>Licence de danse : <i class="erreur" id="is_licence">'.$selected_user['is_licence'].'</i></p>';
			if (isset($selected_user['path_licence']) && $selected_user['is_licence'] === 'en attente'){
				echo '
						<div>
							<input type="radio" id="statut_licenceYes" name="statut_licence" value="valide">
							<label for="statut_licenceYes">valide</label>

							<input type="radio" id="statut_licenceNo" name="statut_licence" value="non valide">

							<input type="radio" checked name="statut_licence" class="hidden" hidden value="noChange">
							<label for="statut_licenceYes">non valide</label>
						</div>
						<div class="docPhoto">
							<img id="path_licence" src="'.$selected_user['path_licence'].'" alt="Photo licence de danse">
						</div>';
			}else{echo '<p class="erreur" hidden>Prévoir d\'afficher/revérifier à la demande.</p>';}
		echo '
		</li>
		<li id=yoga style="order:'.$order_yoga.'">
			<p>Certificat de Yoga : <i class="erreur" id="is_yoga">'.$selected_user['is_yoga'].'</i></p>';
			if (isset($selected_user['path_yoga']) && $selected_user['is_yoga'] === 'en attente'){
				echo '
				<div>
					<input type="radio" id="statut_yogaYes" name="statut_yoga" value="valide">
					<label for="statut_yogaYes">valide</label>

					<input type="radio" id="statut_yogaNo"name="statut_yoga" value="non valide">

					<input type="radio" checked name="statut_yoga" class="hidden" hidden value="noChange">
					<label for="statut_yogaYes">non valide</label>
				</div>
				<div class="docPhoto">
					<img id="path_yoga" src="'.$selected_user['path_yoga'].'" alt="Photo yoga">
				</div>';
			}else{echo '<p class="erreur" hidden>Prévoir d\'afficher/revérifier à la demande.</p>';}
		echo "
		</li>
	</ul>
</div>";
?>