<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Str;

class FirebaseUploader
{
    protected $storage;
    protected $bucket;

    public function __construct()
    {
        $firebasePath = base_path(env('FIREBASE_CREDENTIALS'));

        // ✅ Check if path exists and is a valid file (not directory)
        if (!file_exists($firebasePath) || is_dir($firebasePath)) {
            throw new \Exception("⚠️ Firebase credentials file is missing or is a directory: $firebasePath");
        }

        $this->storage = new StorageClient([
            'keyFilePath' => $firebasePath,
        ]);

        $this->bucket = $this->storage->bucket(env('FIREBASE_STORAGE_BUCKET'));
    }

    public function uploadFile($file, $folder)
    {
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $path = $folder . '/' . $fileName;
        $uuid = (string) Str::uuid();

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

        // Make file public (optional)
        $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);

        // ✅ Return accessible Firebase URL
        return sprintf(
            'https://firebasestorage.googleapis.com/v0/b/%s/o/%s?alt=media&token=%s',
            env('FIREBASE_STORAGE_BUCKET'),
            rawurlencode($path),
            $uuid
        );
    }
}
