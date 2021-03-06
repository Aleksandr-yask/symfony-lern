<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $greet = '';
        if ($name = $request->query->get('hello')) {
            $greet = "Hello $name";
        }

        return new Response(<<<EOF
                    <html>
                        <body>
                        $greet
                            <img src="/images/under-construction.gif" />
                        </body>
                    </html>
EOF
);
    }
}
