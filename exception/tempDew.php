<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8" />
    <title>503 Service Unavailable</title>
</head>

<body>
    <style>
        body {
            background: #333;
            font-family: monospace;
        }

        .container {
            padding: 25px;
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

        .table {
            background: #444;
            border: #777 solid 1px;
            border-radius: 10px;
            color: #00ed35;
            margin-bottom: 25px;            
            width: 100%;
            border-spacing: 0;
            padding-bottom: 10px;
        }

        .table thead tr th {
            background: #222;
            padding: 10px;
            color: #fff;
            margin-bottom: 10px;
        }

        .table tbody tr td {
            padding: 10px;
            transition: 0.5s;
        }

        .table tbody tr {
            transition: 0.5s;
        }

        /* Нечетные строки */
        .table tbody tr:nth-child(odd) {
            background: #444;
        }

        /* Четные строки */
        .table tbody tr:nth-child(even) {
            background: #333;
        }

        .table tbody tr:hover {
            background: #242424;
            color:#fff
        }

        .electronic {
            font-size: 1.2em;
            color:#00ed35;
            text-align: center;
        }
        .electronic a {
            color:#00ed35;
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
            border-radius: 10px 0 0 0 ;
        }
        .brr {
            border-radius: 0 10px 0 0 ;
        }
    </style>
    <div class="container">

        <div class="bl">
            <div class="" role="alert">
                <div class="t"><?= localPathFile($message) ?></div>
                <div>
                    <?= localPathFile($exeption->getFile()) ?>
                    <strong><?= $exeption->getLine() ?></strong>
                </div>
            </div>
        </div>

            <table class="table">
                <thead>
                    <tr class="table-secondary">
                        <th class="brl">№</th>
                        <th scope="col">file</th>
                        <th scope="col">line</th>
                        <th scope="col">class</th>
                        <th scope="col">function</th>
                        <th class="brr" scope="col">arg</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($exeption->getTrace() as $a => $e): ?>
                        <tr>
                            <td class="text-muted"><?= $a + 1 ?></td>
                            <td class="text-primary-emphasis">
                                <?php if(isset($e['file'])): ?>
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
                                                <?php foreach($i as $aa => $ii):?>
                                                    <div><?=$aa?> - <?=gettype($ii)?></div>
                                                <?php endforeach; ?>
                                            <?php elseif(is_string($i)) : ?>
                                                <?=  mb_strimwidth(htmlspecialchars($i), 0, 100, "..."); ?>
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

        <div class="electronic">
            <a href="https://github.com/Grewi/electronic-system3" target="_blank">Electronic</a> | <a href="https://grewi.ru" target="_blank">Grewi</a>
        </div>
    </div>
</body>

</html>