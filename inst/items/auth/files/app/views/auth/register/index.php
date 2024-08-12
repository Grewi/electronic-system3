<use layout="start" />
<block name="index">
    <div id="authContent" class="register-form-wr">
        <h4>Регистрация</h4>
        <form method="post">
            <scrf name="registaration" type="input" />
            <div id="email" class="mb-3">
                <input class="form-control ajax-control <?= $class_email ?>" data-type="email" type="text" name="email" placeholder="Электронная почта" autocomplete="off" value="<?= $email ?>" tabindex="-1">
                <div class="invalid-feedback"><?= $error_email ?></div>
            </div>
            <div class="mb-3">
                <input id="email2" class="form-control ajax-control <?= $class_email2 ?>" data-type="email2" type="text" name="email2" placeholder="Электронная почта" autocomplete="off" value="<?= $email2 ?>" tabindex="1">
                <div class="invalid-feedback"><?= $error_email2 ?></div>
            </div>
            <div class="mb-3">
                <input id="name" type="text" class="form-control ajax-control <?= $class_name ?>" data-type="name" name="name" placeholder="Имя" autocomplete="off" value="<?= $name ?>" tabindex="2">
                <div class="invalid-feedback"><?= $error_name ?></div>
            </div>
            <div class="mb-3">
                <input id="lastname" type="text" class="form-control ajax-control <?= $class_lastname ?>" data-type="lastname" name="lastname" placeholder="Фамилия" autocomplete="off" value="<?= $lastname ?>" tabindex="2">
                <div class="invalid-feedback"><?= $error_lastname ?></div>
            </div>
            <div class="mb-3">
                <input id="middlename" type="text" class="form-control ajax-control <?= $class_middlename ?>" data-type="middlename" name="middlename" placeholder="Отчество" autocomplete="off" value="<?= $middlename ?>" tabindex="2">
                <div class="invalid-feedback"><?= $error_middlename ?></div>
            </div>
            <div class="mb-3" style="display:flex; justify-content: space-between;">
                <div style="width: -moz-available;">
                    <input id="password" type="password" class="form-control ajax-control <?= $class_password ?> pps" data-type="password" name="password" placeholder="Пароль" autocomplete="off" tabindex="3">
                    <div class="invalid-feedback"><?= $error_password ?></div>
                </div>
                <div style="width: fit-content;">
                    <button id="pps" class="btn btn-light" type="button"><i class="bi bi-eye-fill"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <input id="confirm-password" type="password" class="form-control ajax-control <?= $class_confirm_password ?> pps" name="confirm_password" data-type="confirm-password" placeholder="Повтор пароля" autocomplete="off" tabindex="4">
                <div class="invalid-feedback"><?= $error_confirm_password ?></div>
            </div>
            <div class="mb-3">
                <input class="btn btn-primary btn-sm" type="submit" value="Зарегистрироваться" tabindex="5">
                <a href="/login" class="btn btn-secondary btn-sm" tabindex="6">Назад</a>
            </div>
        </form>
    </div>
</block>