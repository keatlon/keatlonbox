<div class="l20 mt10 mb20">
    <div class="p20" style="background-color:#f8e799;">
        <h2><?= __('Системная ошибка')?></h2>
        <br/>
        <? if (conf::i()->log['display']) : ?>
            <?= $errors ?>
        <? else : ?>
            <?= __('К сожалению, в системе произошла ошибка. Уведомление об ошибке отправлено нам автоматически.');?>
        <? endif?>
    </div>
</div>