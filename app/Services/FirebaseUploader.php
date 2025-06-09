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
        $uuid = (string) \Str::uuid();

        $object = $this->bucket->upload(
            fopen($file->getRealPath(), 'r'),
            [
                'name' => $path,
                'metadata' => [
                    'metadata' => [
                        'firebaseStorageDownloadTokens' => $uuid
                    ]
                ]
            ]
        );

        // Optional: make public if you still want direct public access
        $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);

        // Return Firebase-style URL
        return sprintf(
            'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
            env('FIREBASE_STORAGE_BUCKET'),
            rawurlencode($path),
            $uuid
        );
    }
}
