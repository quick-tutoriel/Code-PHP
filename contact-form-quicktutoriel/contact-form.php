<?php
/*
Template Name: Contact Form
*/

function notEmptyValidator($message)
{
    return function ($value) use ($message) {
        if (trim($value) === '') {
            return $message;
        }
    };
}

function emptyValidator($message)
{
    return function ($value) use ($message) {
        if (trim($value) !== '') {
            return $message;
        }
    };
}

function emailValidator($message)
{
    return function ($value) use ($message) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $message;
        }
    };
}

function showError($errors, $field)
{
    if (isset($errors[$field])) {
        echo sprintf('<span class="error"><br>%s</span>', $errors[$field]);
    }
}

$fields = array(
    'submitted' => null,
    'site' => null,
    'checking' => emptyValidator('Laissez ce champ vide.'),
    'contactName' => notEmptyValidator('* Indiquez votre nom.'),
    'sujet' => notEmptyValidator('* Indiquez le sujet de votre mail.'),
    'comments' => notEmptyValidator('* Entrez votre message.'),
    'email' => array(
        notEmptyValidator('* Indiquez une adresse e-mail.'),
        emailValidator('* Indiquez une adresse e-mail valide.'),
    ),
);

$formSubmitted = true;
$formIsValid = true;
$formErrors = array();
$formDatas = array();

foreach ($fields as $field => $v) {
    if (!isset($_POST[$field])) {
        $formSubmitted = false;
    }
}

if ($formSubmitted) {
    foreach ($fields as $field => $validator) {
        $value = $_POST[$field];

        if ($validator !== null) {
            $validation = null;

            if (is_array($validator)) {
                foreach ($validator as $vld) {
                    if ($validation === null) {
                        $validation = $vld($value);
                    }
                }
            } else {
                $validation = $validator($value);
            }
        }

        if ($validation !== null) {
            $formIsValid = false;
            $formErrors[$field] = $validation;
            $formDatas[$field] = null;
        } else {
            $formDatas[$field] = $value;
        }
    }

    if (function_exists('stripslashes')) {
        $formDatas = array_map('stripslashes', $formDatas);
    }
} else {
    foreach ($fields as $field => $v) {
        $formDatas[$field] = null;
    }
}

if ($formSubmitted && $formIsValid) {
    $contactName = $formDatas['contactName'];
    $sujet = $formDatas['sujet'];
    $comments = $formDatas['comments'];
    $site = $formDatas['site'];
    $email = $formDatas['email'];

    $mailBody = <<<EOF
Name: $contactName
Sujet: $sujet
Email: $email
Comments: $comments
EOF;

    $mailRecipient = 'webmaster@quick-tutoriel.com';
    $mailSender = 'Mon site <'.$mailRecipient.'>';
    $mailSenderCopy = 'foo@example.com';
    $mailSubject = '{Contact Quick-Tutoriel} - '.$sujet;
    $doSendCopy = !empty($_POST['sendCopy']);
    $mailHeaders = array(
        'From: '.$mailSender,
        'Reply-To: '.$mailSender,
        'To: '.$mailRecipient,
    );

    mail(
        $mailRecipient,
        $mailSubject,
        $mailBody,
        implode("\r\n", $mailHeaders)
    );

    if ($doSendCopy) {
        $mailHeaders['From'] = $sendCopy;
        $mailHeaders['Reply-To'] = $sendCopy;

        mail(
            $mailRecipient,
            $mailSubject,
            $mailBody,
            implode("\r\n", $mailHeaders)
        );
    }
}

$formDatas = array_map('htmlspecialchars', $formDatas);

?>


<?php get_header(); ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/contact-form.js"></script>

<?php if ($formSubmitted && $formIsValid): ?>
	<div class="thanks">
		<h1>Merci, <?php echo $formDatas['contactName']; ?></h1><br><br>
		<p>Votre e-mail a &eacute;t&eacute; envoy&eacute; avec succ&egrave;s. Vous recevrez une r&eacute;ponse sous peu.</p>
	</div>
<?php else: ?>
	<?php if (have_posts()) : ?>
	    <?php while (have_posts()) : the_post(); ?>
		    <h1><?php the_title(); ?></h1>
		    <br/>

		    <?php the_content(); ?>

            <?php if ($formSubmitted && !$formIsValid): ?>
                <p class="errorForms">Une erreur est survenue lors de l'envoi du formulaire.</p>
                <br/>
            <?php endif; ?>

            <form action="<?php the_permalink(); ?>" id="contactForm" method="post">
                <ol class="forms">
                    <li>
                        <label for="contactName">Votre nom (requis) :</label><br><br>
                        <input type="text" name="contactName" id="contactName" value="<?php echo $formDatas['contactName'] ?>" class="requiredField" />
                        <?php showError($formErrors, 'contactName') ?>
                    </li>

                    <li>
                        <label for="email">Votre e-mail (requis) :</label><br><br>
                        <input type="text" name="email" id="email" value="<?php echo $formDatas['email'] ?>" class="requiredField email" />

                        <?php showError($formErrors, 'email') ?>
                    </li>

                    <li>
                        <label for="site">Votre site web :</label><br><br>
                        <input type="text" name="site" id="site" value="<?php echo $formDatas['site'] ?>" />

                        <?php showError($formErrors, 'site') ?>
                    </li>

                    <li>
                        <label for="sujet">Sujet (requis) :</label><br><br>
                        <input type="text" name="sujet" id="sujet" value="<?php echo $formDatas['sujet'] ?>" class="requiredField" />

                        <?php showError($formErrors, 'sujet') ?>
                    </li>

                    <li class="textarea">
                        <label for="commentsText">Message (requis) :</label><br><br>
                        <textarea name="comments" id="commentsText" class="requiredField"><?php echo $formDatas['comments'] ?></textarea>
                        <?php showError($formErrors, 'comments') ?>
                    </li>
                    <li class="inline">
                        <input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if (isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) {
    echo ' checked="checked"';
} ?> />
                        <label for="sendCopy">&nbsp;Recevoir une copie du message</label>
                    </li>
                    <li class="screenReader">
                        <label for="checking" class="screenReader">Pour envoyer ce formulaire, ne saisissez RIEN dans ce champ</label>
                        <input type="text" name="checking" id="checking" class="screenReader" value="<?php if (isset($_POST['checking'])) {
    echo $_POST['checking'];
} ?>" />
                        <?php showError($formErrors, 'checking') ?>
                    </li>
                    <li class="buttons">
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                        <button type="submit">Envoyer</button>
                        <input type="hidden" name="resume" id="resume" value="true"/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="reset">Reset</button>
                    </li>
                </ol>
            </form>
		<?php endwhile; ?>
	<?php endif; ?>
<?php endif; ?>

<?php get_footer(); ?>
