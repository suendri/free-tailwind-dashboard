<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Throwable;

class SaveUserPhoto
{
    public function execute(User $user, UploadedFile $photo): string
    {
        $uploadPath = public_path('uploads/user');
        $fileName = Str::uuid().'.jpg';
        $targetPath = $uploadPath.DIRECTORY_SEPARATOR.$fileName;
        $previousPhoto = $user->photo;

        File::ensureDirectoryExists($uploadPath);

        $manager = new ImageManager(new GdDriver);
        $manager
            ->decodePath($photo->getRealPath())
            ->cover(320, 320)
            ->save($targetPath, quality: 85);

        try {
            $user->update(['photo' => $fileName]);
        } catch (Throwable $exception) {
            File::delete($targetPath);

            throw $exception;
        }

        if ($previousPhoto !== null) {
            File::delete($uploadPath.DIRECTORY_SEPARATOR.$previousPhoto);
        }

        return $fileName;
    }
}
