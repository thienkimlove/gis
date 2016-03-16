@servers(['local' => 'root@127.0.0.1'])
@setup
    $sql_file = 'gis_backup_' . date('YmdHis') . '.sql';
    $backup_dir = '/root/transfers';
    $db_name = 'zukosha_for_demo';
    $db_user = 'postgres';
    $db_pass = '12345';
@endsetup

@task('backup', ['on' => 'local'])
   echo "Running..";
   [ -d {{ $backup_dir }} ] || mkdir {{ $backup_dir }};
   /usr/pgsql-9.4/bin/pg_dump --dbname=postgresql://{{$db_user}}:{{$db_pass}}@127.0.0.1:5432/{{$db_name}} -f {{$backup_dir}}/{{$sql_file}};
@endtask
