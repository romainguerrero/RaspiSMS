<?php
	//Template dashboard
	$incs = new internalIncs();
	$incs->head('Scheduleds - Add');
?>
<div id="wrapper">
<?php
	$incs->nav('scheduleds');
?>
	<div id="page-wrapper">
		<div class="container-fluid">
			<!-- Page Heading -->
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Nouveau SMS programmé
					</h1>
					<ol class="breadcrumb">
						<li>
							<i class="fa fa-dashboard"></i> <a href="<?php echo $this->generateUrl('dashboard'); ?>">Dashboard</a>
						</li>
						<li>
							<i class="fa fa-calendar"></i> <a href="<?php echo $this->generateUrl('scheduleds'); ?>">Scheduleds</a>
						</li>
						<li class="active">
							<i class="fa fa-plus"></i> Nouveau
						</li>
					</ol>
				</div>
			</div>
			<!-- /.row -->

			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fa fa-calendar fa-fw"></i> Ajout d'un SMS programmé</h3>
						</div>
						<div class="panel-body">
							<form action="<?php echo $this->generateUrl('scheduleds', 'create', [$_SESSION['csrf']]);?>" method="POST">
								<div class="form-group">
									<label>Texte du SMS</label>
									<textarea name="content" class="form-control" required></textarea>
								</div>
								<div class="form-group">
									<label>Date d'envoi du SMS</label>
									<input name="date" class="form-control form-datetime" type="text" value="<?php secho($now); ?>" readonly>
								</div>
								<div class="form-group">
									<label>Numéros cibles</label>
									<div class="form-group scheduleds-number-group-container">
										<!-- <div class="form-group scheduleds-number-group">
											<input name="" class="form-control phone-international-input" type="tel" >
											<span class="remove-scheduleds-number fa fa-times"></span>
											<input name="numbers[]" type="hidden" class="phone-hidden-input">
										</div> -->
										<div class="add-number-button fa fa-plus-circle"></div>
									</div>
								</div>
								<div class="checkbox">
								    <label>
								    	<input type="checkbox" id="all-contacts" name="all-contacts" value=1> <strong>Envoyer à tout le répertoire</strong>
								    </label>
							  	</div>
								<div class="form-group">
									<label>Contacts cibles</label>
									<input class="add-contacts form-control" name="contacts[]"/>
								</div>
								<div class="form-group">
									<label>Groupes cibles</label>
									<input class="add-groups form-control" name="groups[]"/>
								</div>
								<?php if (RASPISMS_SETTINGS_SMS_FLASH) { ?>
									<div class="form-group">
										<label>Envoyer comme un SMS Flash : </label>
										<div class="form-group">
											<input name="admin" type="radio" value="1" required /> Oui
											<input name="admin" type="radio" value="0" required checked/> Non
										</div>
									</div>
								<?php } ?>
								<a class="btn btn-danger" href="<?php echo $this->generateUrl('scheduleds'); ?>">Annuler</a>
								<input type="submit" class="btn btn-success" value="Enregistrer le SMS" />
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function()
	{
		<?php
			if (RASPISMS_SETTINGS_EXTENDED_CONTACTS_INFOS) {
				$magicSuggestRenderer = "(data['contacts_infos.civility']!=null ? (data['contacts_infos.civility']==1 ? 'M. ' : 'Mme ') : '')";
				$magicSuggestRenderer .= " + data['contacts.name']";
				$magicSuggestRenderer .= " + (data['contacts_infos.birthday']!=null ? ' (' + age(data['contacts_infos.birthday']) + ' ans)' : '')";
			} else {
				$magicSuggestRenderer = "data['name']";
			}
		?>

		// Affiche plus d'infos que le nom du contact si on est en mode infos contacts
		jQuery('.add-contacts').each(function()
		{
			magicContacts = jQuery(this).magicSuggest({
				data: '<?php echo $this->generateUrl('contacts', 'jsonGetContacts'); ?>',
				valueField: '<?php echo RASPISMS_SETTINGS_EXTENDED_CONTACTS_INFOS ? 'contacts.' : ''; ?>id',
				displayField: '<?php echo RASPISMS_SETTINGS_EXTENDED_CONTACTS_INFOS ? 'contacts.' : ''; ?>name',
				name: 'contacts[]',
				maxSelection: null, //autorise plus de 10 sélections
				allowFreeEntries: false, // évite que l'utilisateur ne saisisse autre chose qu'un contact de la liste
				renderer: function(data) {
		            return <?php echo $magicSuggestRenderer; ?>;
		        }
			});
		});

		function age(birthday)
		{
		  	birthday = new Date(birthday);
		  	return new Number((new Date().getTime() - birthday.getTime()) / 31536000000).toFixed(0);
		}

		jQuery('.add-groups').each(function()
		{
			magicGroups = jQuery(this).magicSuggest({
				data: '<?php echo $this->generateUrl('groups', 'jsonGetGroups'); ?>',
				valueField: 'id',
				displayField: 'name',
				maxSelection: null, //autorise plus de 10 sélections
			});
		});

		jQuery('.phone-international-input').intlTelInput({
			defaultCountry: '<?php secho(RASPISMS_SETTINGS_DEFAULT_PHONE_COUNTRY); ?>',
			preferredCountries: <?php secho(json_encode(explode(',', RASPISMS_SETTINGS_PREFERRED_PHONE_COUNTRY)), false, false); ?>,
			nationalMode: true,
			utilsScript: '<?php echo HTTP_PWD; ?>/js/intlTelInput/lib/libphonenumber/utils.js'
		});

		jQuery('body').on('click', '.remove-scheduleds-number', function(e)
		{
			jQuery(this).parents('.scheduleds-number-group').remove();
		});

		jQuery('body').on('click', '.add-number-button', function(e)
		{
			var newScheduledsNumberGroup = '' +
			'<div class="form-group scheduleds-number-group">' +
				'<input name="" class="form-control phone-international-input" type="tel" >' +
				'<span class="remove-scheduleds-number fa fa-times"></span>' +
				'<input name="numbers[]" type="hidden" class="phone-hidden-input">' +
			'</div>';

			jQuery(this).before(newScheduledsNumberGroup);

			jQuery('.phone-international-input').intlTelInput({
				defaultCountry: '<?php secho(RASPISMS_SETTINGS_DEFAULT_PHONE_COUNTRY); ?>',
				preferredCountries: <?php secho(json_encode(explode(',', RASPISMS_SETTINGS_PREFERRED_PHONE_COUNTRY)), false, false); ?>,
				nationalMode: true,
				utilsScript: '<?php echo HTTP_PWD; ?>/js/intlTelInput/lib/libphonenumber/utils.js'
			});

		});

		jQuery('.form-datetime').datetimepicker(
		{
			format: 'yyyy-mm-dd hh:ii:ss',
			autoclose: true,
			minuteStep: 1,
			language: 'fr'
		});


		jQuery('form').on('submit', function(e)
		{
			e.preventDefault();
			jQuery('.phone-international-input').each(function(key, value)
			{
				var container = jQuery(this).parents('.scheduleds-number-group');
				container.find('.phone-hidden-input').val(jQuery(this).intlTelInput("getNumber"));
			});

			this.submit();
		});

		$("#all-contacts").on('change', function() {
			if ($(this).is(':checked')) {
				magicContacts.disable();
				magicGroups.disable();
			} else {
				magicContacts.enable();
				magicGroups.enable();
			}
		})
	});
</script>
<?php
	$incs->footer();
