<?php
// Корневой каталог
$root_directory = $_SERVER['DOCUMENT_ROOT'] . '/kolesa1';

// Какие форматы файлов не проверять
$exclude_file = array('php', 'txt');

// Определяем расширение файла
function get_file_type($path)
{
    $path_info = pathinfo($path);
    return $path_info['extension'];
}

// Главная функция, проверяет файлы
function check_file($dir = "./")
{
    global $root_directory, $exclude_file;
    $result = array();
    $data_array = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (is_file("$dir/$file")) {
                    $time_change = filemtime("$dir/$file");
                    $time_change_index = filectime("$dir/$file");
                    $format_file = get_file_type("$dir/$file");

                    if (!in_array($format_file, $exclude_file)) {
                        $data_array['time_change'] = date('d.m.Y H:i', $time_change);
                        $data_array['time_change_index'] = date('d.m.Y H:i', $time_change_index);
                        $data_array['path'] = str_replace($root_directory . '/', '', "$dir/$file");
                        $result[] = $data_array;
                    };
                }
            }
            closedir($dh);
        };
        return $result;
    };
};

// запись данные файла в текстовый файл
function write_to_file($res){
    global $root_directory;
    $file_name = $root_directory . '/log_files.txt';
    foreach ($res as $row) {
        $file_content = $row['time_change'] . '|' . $row['time_change_index'] . '|' . $row['path'] . "\n";
    };
    //Проверка на существование файла
    if (is_writable($file_name)) {

        if (!$handle = fopen($file_name, 'a')) {
            echo "Не могу открыть файл ($file_name)";
            exit;
        }

        // Записываем данные в наш открытый файл.
        if (fwrite($handle, $file_content) === FALSE) {
            echo "Не могу произвести запись в файл ($file_name)";
            exit;
        }
        echo "Запись завершена успешно";
        fclose($handle);

    } else {
        echo "Файл $file_name недоступен для записи";
    }
}

$result = check_file($root_directory);

if (!$result) {
    echo "Изменений не было";
    exit;
}
else{
    write_to_file($result);
}

?>