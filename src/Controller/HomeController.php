<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        
		$url = 'https://raw.githubusercontent.com/RashitKhamidullin/Educhain-Assignment/refs/heads/main/get-documents'; 


		$ch = curl_init($url);


		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json', 
		]);


		$response = curl_exec($ch);


		if (curl_errno($ch)) {
			echo 'cURL Error: ' . curl_error($ch) . PHP_EOL;
			curl_close($ch);
			exit; 
		}

		
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		
		if ($httpStatus >= 200 && $httpStatus < 300) {
			
			$data = json_decode($response, true);
			
			print_r($data);
			
			if (json_last_error() === JSON_ERROR_NONE) {
				
				echo "Data fetched successfully!" . PHP_EOL;
				foreach($data as $datas){
					$certificate = $datas['certificate'];
					$description = $datas['description'];
					$doc_no = $datas['doc_no'];
					$content = base64_decode($datas['certificate']);
					$uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/';
					if (!is_dir($uploadsDirectory)) {
						mkdir($uploadsDirectory, 0777, true);
					}
					$fileName = $description.'_'.$doc_no.'.pdf';
					$filePath = $uploadsDirectory.$fileName;
					file_put_contents($filePath, $content);
					echo "PDF file generated and moved successfully!";
				}
			} else {
				echo "JSON Decode Error: " . json_last_error_msg() . PHP_EOL;
			}
		} else {
			echo "HTTP Request failed with status code: $httpStatus" . PHP_EOL;
			echo "Response: " . $response . PHP_EOL;
		}
		return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
