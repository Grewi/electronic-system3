<?php

use system\core\app\app;

if (!function_exists('systemPanel')) {
    function systemPanel()
    {
        $app = app::app();
        if (getConfig('globals', 'dev') != 1 || getConfig('globals', 'system-panel') != 1) {
            return '';
        }
?>
        <style>
            body {
                padding-bottom: 30px;
            }

            .system-panel {
                height: 30px;
                background: #00580cff;
                color: #7db185ff;
                position: fixed;
                bottom: 0;
                left: 0;
                z-index: 999999;
                color: #fff;
                padding: 5px 10px;
                border-top-right-radius: 3px;
                font-family: monospace;
            }

            .sp-code {
                color: #7db185ff;
                background: #003f06ff;
                padding: 3px 10px;
                border-radius: 2px;
            }

            .sp-sep {
                margin: 0 10px;
                color: #002904ff;
            }

            .sp-name {
                color: #7db185ff;
            }
        </style>
        <div class="system-panel">
            <span class="sp-name">Роутер</span>
            <code class="sp-code">
                <?php if ($app->route->group): ?>
                    <span style="color:#c8ce8b"><?= $app->route->group ?></span>
                <?php endif; ?>
                <?= $app->route->mask ?>
            </code>
            <span class="sp-sep">|></span>
            <span class="sp-name">Контроллер</span>
            <code class="sp-code">
                <?= $app->controller->class ?>::<?= $app->controller->method ?>
            </code>
            <span class="sp-sep">|></span>
            <span class="sp-name">Шаблон</span>
            <code class="sp-code">
                <?php if ($app->view->layout): ?>
                    <span style="color:#c8ce8b"><?= $app->view->layout ?></span>
                <?php endif; ?>
                <?= $app->views->{0} ?>
            </code>
        </div>
    <?php
    }
}


if (!function_exists('systemPanelModal')) {
    function systemPanelModal()
    {
        $app = app::app();
        if (getConfig('globals', 'dev') != 1 || getConfig('globals', 'system-panel') != 1) {
            return '';
        }
    ?>
        <style>
            .system-panel-m {
                width: 100%;
                background: #00580cff;
                color: #7db185ff;
                z-index: 999999;
                color: #fff;
                padding: 5px 10px;
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px;
                font-family: monospace;
                font-size: 10px;
            }

            .spm-code {
                color: #7db185ff;
                background: #003f06ff;
                padding: 3px 10px;
                border-radius: 2px;
            }

            .spm-sep {
                margin: 0 10px;
                color: #002904ff;
            }

            .spm-name {
                color: #7db185ff;
            }
        </style>
        <div class="system-panel-m">
            <div>
                <span class="spm-name">Роутер</span>
                <code class="spm-code">
                    <?php if ($app->route->group): ?>
                        <span style="color:#c8ce8b"><?= $app->route->group ?></span>
                    <?php endif; ?>
                    <?= $app->route->mask ?>
                </code>
            </div>
            <div>
                <span class="spm-name">Контроллер</span>
                <code class="spm-code">
                    <?= $app->controller->class ?>::<?= $app->controller->method ?>
                </code>
            </div>
            <div>
                <span class="spm-name">Шаблон</span>
                <code class="spm-code">
                    <?php if ($app->view->layout): ?>
                        <span style="color:#c8ce8b"><?= $app->view->layout ?></span>
                    <?php endif; ?>
                    <?= $app->views->{0} ?>
                </code>
            </div>
        </div>
<?php
    }
}
