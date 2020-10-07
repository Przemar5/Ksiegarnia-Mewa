<?php

namespace App\Services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FileUploader
{
	private $filesystem;

    private ?array $nonRemovableFiles;

    private ?string $baseDir;

	public function __construct(ContainerInterface $container)
	{
		$this->filesystem = new Filesystem();
	}

    public function exists(string $path)
    {
        return $this->filesystem->exists($path);
    }

	public function upload(UploadedFile $file, string $path)
	{
        $filename = time() . '_' . uniqid() . '.' . $file->guessClientExtension();

        $file->move($this->baseDir.$path, $filename);

        return $filename;
	}

	public function delete(string $path)
    {
    	$fullPath = $this->baseDir.$path;
    	
    	if (!$this->filesystem->exists($fullPath)) {
    		return;
    	}

        $result = $this->filesystem->remove($fullPath);

        if ($result === false) {
            throw new \Exception(sprintf('Error deleting "%s"', $path));
        }
    }

    public function setNonRemovableFiles(array $nonRemovableFiles): void
    {
        $this->nonRemovableFiles = $nonRemovableFiles;
    }

    public function getBaseDir(): ?string
    {
        return $this->baseDir;
    }

    public function setBaseDir(string $baseDir): void
    {
        $this->baseDir = $baseDir;
    }
}