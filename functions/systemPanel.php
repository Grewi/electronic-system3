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
                z-index: 999999999;
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

            .system-panel-block {
                font-family: monospace;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: #003f06ff;
                overflow: auto;
                z-index: 999999998;
            }

            .sp-btn {
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

            .sp-col ul {
                max-height: 400px;
                overflow: auto;
            }

            .sp-col ul li {
                padding: 3px;
                border: 1px solid #00580cff;
                border-radius: 3px;
                margin-bottom: 3px;
                color: #c8ce8b;
                display: flex;
                justify-content: space-between;
            }

            .sp-col ul li span {
                max-width: 85%;
                min-width: 15%;
                background: #01400a;
            }

            /**/
            .sp-container {
                padding: 25px;
                padding-bottom: 50px;
            }

            .bl {
                background: #444;
                padding: 20px;
                border: #777 solid 1px;
                border-radius: 10px;
                color: #00ed35;
                margin-bottom: 25px;
            }

            .t {
                font-size: 1.5em;
                margin-bottom: 10px;
            }

            .sp-table {
                background: #444;
                border: #777 solid 1px;
                border-radius: 10px;
                color: #00ed35;
                margin-bottom: 25px;
                width: 100%;
                border-spacing: 0;
                padding-bottom: 10px;
            }

            .sp-table thead tr th {
                background: #222;
                padding: 10px;
                color: #fff;
                margin-bottom: 10px;
            }

            .sp-table tbody tr td {
                padding: 10px;
                transition: 0.5s;
            }

            .sp-table tbody tr {
                transition: 0.5s;
            }

            /* Нечетные строки */
            .sp-table tbody tr:nth-child(odd) {
                background: #444;
            }

            /* Четные строки */
            .sp-table tbody tr:nth-child(even) {
                background: #333;
            }

            .sp-table tbody tr:hover {
                background: #242424;
                color: #fff
            }

            .electronic {
                margin: 15px 0;
                font-size: 1.2em;
                color: #00ed35;
                text-align: center;
            }

            .electronic a {
                color: #00ed35;
                text-decoration: none;
                transition: 0.5s;
                padding: 5px;
                border: 1px solid transparent;
            }

            .electronic a:hover {
                border: 1px solid #444;
                background: #222;
            }

            .brl {
                border-radius: 10px 0 0 0;
            }

            .brr {
                border-radius: 0 10px 0 0;
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
            <span id="system-panel-vis" class="sp-btn">$</span>
            <span id="system-panel-trace" class="sp-btn">♯</span>
        </div>
        <div id="system-panel-full" style="display:none" data-status="0" class="system-panel-block">
            <div class="electronic">
                <a href="https://github.com/Grewi/electronic-system3" target="_blank">Electronic</a> | <a href="https://grewi.ru" target="_blank">Grewi</a>
            </div>
            <!-- <div class="sp-el">ELECTRONIC</div> -->
            <div class="sp-wr">
                <div class="sp-col">
                    <p>Шаблоны</p>
                    <ul>
                        <?php foreach ($app->views->getArray() as $view): ?>
                            <li><?= $view ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- <p>Переменные запроса</p>
                    <ul>
                        <?php foreach ($app->getparams->getArray() as $name => $view): ?>
                            <li><?= $name ?> <?= $view ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Bootstrap</p>
                    <ul>
                        <?php foreach ($app->bootstrap->getArray() as $name => $view): ?>
                            <li><b><?= $name ?></b> <?= $view ?></li>
                        <?php endforeach; ?>
                    </ul> -->
                </div>
                <div class="sp-col">
                    <p>Время</p>
                    <ul>
                        <?php foreach ($app->time->getArray() as $name => $view): ?>
                            <li>
                                <span><?= $name ?></span> <span><?= $view ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sp-col">
                    <p>Память</p>
                    <ul>
                        <?php foreach ($app->memory->getArray() as $name => $view): ?>
                            <li>
                                <span><?= $name ?></span> <span><?= $view ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div id="system-panel-table" style="display:none" data-status="0" class="system-panel-block">
            <div class="electronic">
                <a href="https://github.com/Grewi/electronic-system3" target="_blank">Electronic</a> | <a href="https://grewi.ru" target="_blank">Grewi</a>
            </div>
            <div class="sp-container">
                <table class="sp-table">
                    <thead>
                        <tr class="sp-table-secondary">
                            <th class="brl">№</th>
                            <th scope="col">file</th>
                            <th scope="col">line</th>
                            <th scope="col">class</th>
                            <th scope="col">function</th>
                            <th class="brr" scope="col">arg</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach (debug_backtrace() as $a => $e): ?>
                            <tr>
                                <td class="text-muted"><?= $a + 1 ?></td>
                                <td class="text-primary-emphasis">
                                    <?php if (isset($e['file'])): ?>
                                        <?= localPathFile($e['file']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= isset($e['line']) ? $e['line'] : '' ?></td>
                                <td class="fst-italic text-success"><?= isset($e['class']) ? $e['class'] : '' ?></td>
                                <td class="fst-italic text-danger"><?= isset($e['function']) ? $e['function'] : '' ?></td>
                                <td>
                                    <?php if (isset($e['args'])): ?>
                                        <?php foreach ($e['args'] as $i): ?>
                                            <div class="text-muted">
                                                <?php if (is_array($i)): ?>
                                                    <?php foreach ($i as $aa => $ii): ?>
                                                        <div><?= $aa ?> - <?= gettype($ii) ?></div>
                                                    <?php endforeach; ?>
                                                <?php elseif (is_string($i)) : ?>
                                                    <?= mb_strimwidth(htmlspecialchars($i), 0, 100, "..."); ?>
                                                <?php else: ?>
                                                    ---
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            document.getElementById('system-panel-vis').addEventListener('click', function() {
                let a = document.getElementById('system-panel-full');
                let s = a.getAttribute('data-status');
                spActionOpen(a, s);
            });

            document.getElementById('system-panel-trace').addEventListener('click', function() {
                let a = document.getElementById('system-panel-table');
                let s = a.getAttribute('data-status');
                spActionOpen(a, s);
            });

            function spActionOpen(a, s) {
                spActionClosedAll();
                console.log(a, s);
                if (s == '0') {
                    a.style.display = 'block';
                    a.setAttribute('data-status', '1');
                }
            }

            function spActionClosedAll() {
                let a = {};
                a.table = document.getElementById('system-panel-table');
                a.full = document.getElementById('system-panel-full');
                for (key in a) {
                    a[key].style.display = 'none';
                    a[key].setAttribute('data-status', '0');
                }
            }
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
                z-index: 999999999;
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
