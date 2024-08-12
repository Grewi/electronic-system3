<use layout="index" />

<block name="index">
    <div class="container">
        <?php if (user_id() > 0): ?>
            <div>
                Добро пожаловать, <?=$app->user->login?>! 
            </div>
            <div>
                <a class="btn btn-primary" href="/exit" >Выход</a>
            </div>
        <?php else: ?>
            <div class="mt-1">
                <form method="post">
                    <csrf type="input" name="auth" />
                    <input name="auth" value="1" hidden />
                    <div class="mb-3">
                        <input class="form-control" type="text" name="login" placeholder="<?= $lang->register('login') ?>"
                            value="<?= $return->data->login ?>">
                    </div>
                    <div class="mb-3">
                        <input class="form-control" type="email" name="email" placeholder="<?= $lang->register('email') ?>"
                            value="<?= $return->data->email ?>">
                    </div>
                    <div class="mb-3">
                        <input class="form-control" type="password" name="pass" placeholder="<?= $lang->register('pass') ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <input class="btn btn-primary" type="submit" value="<?= $lang->global('login') ?>">
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</block>