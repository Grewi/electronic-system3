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

            #system-panel-full {
                font-family: monospace;
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: #003f06ff;
                overflow: auto;
            }

            #system-panel-vis {
                background: rgb(1, 48, 5);
                color: #7db185ff;
                padding: 2px 5px;
                margin-left: 10px;
                border-radius: 3px;
                cursor: pointer;

            }

            .sp-el {
                text-align: center;
                font-size: 25px;
                color: #7db185ff;
                padding: 15px;
            }

            .sp-wr {
                display: flex;
            }

            .sp-col {
                flex: 33;
                color: #7db185ff;
                padding: 15px;
            }

            .sp-col p {
                font-size: 1.2em;
                color: #7db185ff;
            }

            .sp-col ul li {
                padding: 3px;
                border: 1px solid #00580cff;
                border-radius: 3px;
                margin-bottom: 3px;
                color: #c8ce8b;
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
            <span id="system-panel-vis">
                *
            </span>
        </div>
        <div id="system-panel-full" style="display:none" data-status="0">
            <div class="sp-el">ELECTRONIC</div>
            <div class="sp-wr">
                <div class="sp-col">
                    <p>Шаблоны</p>
                    <ul>
                        <?php foreach ($app->views->getArray() as $view): ?>
                            <li><?= $view ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sp-col">
                    <p>Время</p>
                    <ul>
                        <?php foreach ($app->time->getArray() as $name => $view): ?>
                            <li><?= $name ?> -> <?= $view ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sp-col">
                    <p>Память</p>
                    <ul>
                        <?php foreach ($app->memory->getArray() as $name => $view): ?>
                            <li><?= $name ?> -> <?= $view ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('system-panel-vis').addEventListener('click', function() {
                let a = document.getElementById('system-panel-full');
                let s = a.getAttribute('data-status');
                if (s == '0') {
                    a.style.display = 'block';
                    a.setAttribute('data-status', '1');
                } else {
                    a.style.display = 'none';
                    a.setAttribute('data-status', '0');
                }

            });
        </script>
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
