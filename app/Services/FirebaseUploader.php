<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;

class FirebaseUploader
{
    protected $storage;

    protected $bucket;

    public function __construct()
    {
        $this->storage = new StorageClient([
            'keyFilePath' => base_path(env('FIREBASE_CREDENTIALS')),
        ]);

        $this->bucket = $this->storage->bucket(env('FIREBASE_STORAGE_BUCKET'));
    }

    public function uploadFile($file, $folder)
    {
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $path = $folder . '/' . $fileName;

        $object = $this->bucket->upload(
            fopen($file->getRealPath(), 'r'),
            ['name' => $path]
        );

        // Make public readable
        $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);

        // ✅ Return Public URL
        return sprintf('https://storage.googleapis.com/%s/%s', env('FIREBASE_STORAGE_BUCKET'), $path);
    }
}
