<?php

/** @var int $frameId */
/** @var string $srcToFiles */
/** @var int $btnId */
/** @var int $inputId */
/** @var string $imageContainer */
/** @var string $insertedData */
/** @var string $thumb */
/** @var string $owner */
/** @var int $ownerId */
/** @var string $ownerAttribute */
/** @var string $subDir */
?>

<div role="filemanager-modal" class="modal" tabindex="-1"
     data-frame-id="<?php echo $frameId ?>"
     data-src-to-files="<?php echo $srcToFiles ?>"
     data-btn-id="<?php echo $btnId ?>"
     data-input-id="<?php echo $inputId ?>"
     data-image-container="<?php echo isset($imageContainer) ? $imageContainer : '' ?>"
     data-inserted-data="<?php echo isset($insertedData) ? $insertedData : '' ?>"
     data-thumb="<?php echo $thumb ?>"
     data-owner="<?php echo $owner ?>"
     data-owner-id="<?php echo $ownerId ?>"
     data-owner-attribute="<?php echo $ownerAttribute ?>"
     data-sub-dir="<?php echo $subDir ?>"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>