<?php declare(strict_types=1);

namespace App\Service;

use App\Helper\RandomStringGenerator;
use Exception;
use Jdenticon\Identicon;

class IdenticonGenerator
{
    public function __construct(
        private string $avatarsDirectory,
    ){}

    /**
     * @throws Exception
     */
    public function generate(string $username): string
    {
        $identicon = new Identicon([
            'value' => RandomStringGenerator::generate(6),
        ]);

        $dataUri = str_replace('data:image/png;base64,', '', $identicon->getImageDataUri());
        $data = base64_decode($dataUri);
        $file = $this->avatarsDirectory.'/'.$username.'.png';

        if (!file_exists($this->avatarsDirectory)) {
            mkdir($this->avatarsDirectory, 0755, true);
        }

        if (false === file_put_contents($file, $data)) {
            throw new Exception('Error writing file.');
        }

        return substr($file, strpos($file, '/images'));
    }
}
