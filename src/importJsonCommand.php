<?php


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class importJsonCommand extends Command
{

protected function configure()
{

        $this
            ->setName('import')
            ->addArgument('dbname',InputArgument::REQUIRED, 'Name of DB')
            ->addArgument('host',InputArgument::REQUIRED, 'Host')
            ->addArgument('user',InputArgument::REQUIRED, 'Name of user')
            ->addArgument('password',InputArgument::REQUIRED, 'Password from DB');


}

protected function execute(InputInterface $input, OutputInterface $output)
{
    $data = file_get_contents("https://paste.laravel.io/d716e510-8724-424f-8676-cad2c1986547/raw");
    $data = strip_tags($data);
    $data = preg_replace('/&quot;/', '"', $data);
    $data = json_decode($data, true);
    $user = $input->getArgument('user');
    $host = $input->getArgument('host');
    $dbname = $input->getArgument('dbname');
    $password = $input->getArgument('password');



    $mysqli = @mysqli_connect($host,$user,$password,$dbname);

    $sql = 'CREATE TABLE IF NOT EXISTS json(
              model text,
              color text,
              transmission text,
              price text,
              km text,
              owners text,
              power text,
              engineCapacity text);
            INSERT INTO json (model,color,transmission,price,km,owners,power,engineCapacity)
            VALUES ';
    $count = 0;
    $len = count($data);
    foreach ($data as $key => $string) {
        if($count == $len - 1) {
            $sql .= '("'.$string['model'].'","'.$string['color'].'","'.$string['transmission'].'","'.$string['price'].'","'.$string['km'].'","'.$string['owners'].'","'.$string['power'].'","'.$string['engineCapacity'].'")';
        }
        else {
            $sql .= '("'.$string['model'].'","'.$string['color'].'","'.$string['transmission'].'","'.$string['price'].'","'.$string['km'].'","'.$string['owners'].'","'.$string['power'].'","'.$string['engineCapacity'].'"),
            ';
        }
        $count++;

    }
     mysqli_multi_query($mysqli,$sql);


    $output->writeln('Complete');


    mysqli_close($mysqli);



}
}