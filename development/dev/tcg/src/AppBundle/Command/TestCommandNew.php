<?php
/**
 * Created by PhpStorm.
 * User: Mr.Clock
 * Date: 2015/5/11
 * Time: 23:10
 */
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class TestCommand extends ContainerAwareCommand
{
    protected $logger;

    protected function configure()
    {
        $this
            ->setName('AppBundle:test')
            ->setDescription('Process Service Information.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Please provide the "type".'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $this->logger = new Logger('TestCommand');
            $this->logger->pushHandler(new StreamHandler($this->getContainer()->getParameter('log_dir') . 'TestCommand.log'));
            $type = $input->getArgument('type');
            $text = 'Type - ' . $type . ' Started';
            if ($type === 'email') {
                $output->writeln($text);
                $this->TestEmail();
                $text = 'Type - ' . $type . ' Finish';
			}else if ($type === 'pdf') {
                $output->writeln($text);
                $this->TestPDF();
                $text = 'Type - ' . $type . ' Finish';
            } else {
                $text = 'Type is missing.';
            }

            $output->writeln($text);
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());
            $this->logger->addError($e->getFile() . " " . $e->getLine());
            $this->logger->addError($e->getCode());
            $this->logger->addError($e->getTraceAsString());
        }
    }
	
	private function TestPDF(){
		$this->getContainer()->get('knp_snappy.pdf')->generateFromHtml(
			 $this->getContainer()->get('templating')->render(
				'AppBundle:email:testpdf.html.twig',
                    array('title' => 'adfads','headerImage'=>'adfadf')
				),
				'C:\\xampp\\htdocs\\GitHub\\ThatCleanGirl\\development\\dev\\tcg\\web\PDF\\test1.pdf',
				array('page-size'=>'A4','header-left'=>'Left')
		);
		/*$this->getContainer()->get('knp_snappy.pdf')->generateFromHtml(
				'http://www.google.fr',
				'C:\\xampp\\htdocs\\GitHub\\ThatCleanGirl\\development\\dev\\tcg\\web\PDF\\test1.pdf',
				array('orientation'=>'Landscape','lowquality'=>false)
		);*/
	}

    private function TestEmail(){
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('thatcleangirl@gmail.com')
            ->setTo('zhongyp.design@gmail.com')
            ->setBody(
                $this->getContainer()->get('templating')->render(
                    'AppBundle:email:test_pdf.html',
                    array('title' => 'adfads','headerImage'=>'adfadf')
                )
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;
        $this->getContainer()->get('mailer')->send($message);

    }

}
