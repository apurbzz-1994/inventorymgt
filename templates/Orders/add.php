<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var \Cake\Collection\CollectionInterface|string[] $products
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Orders'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="orders form content">
            <?= $this->Form->create($order) ?>
            <fieldset>
                <legend><?= __('Add Order') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('description');

                    // foreach ($products as $key => $product) {
                    //     echo $this->Form->control('products.'.$key.'.id', ['type' => 'checkbox', 'hiddenField' => false, 'value' => $product->id, 'label'=> $product->name]);
                    //     echo $this->Form->control('products.'.$key.'._joinData.quantity', ['novalidate'=>true]);
                    // }
                ?>
            </fieldset>
            <table>
                        <tr>
                            <th><?= __('Add to order?') ?></th>
                            <th><?= __('Product Name') ?></th>
                            <th><?= __('Stock available') ?></th>
                            <th><?= __('Quantity') ?></th>
                        </tr>
                        <?php foreach ($products as $key=>$product) : ?>
                        <tr>
                            <td><?= $this->Form->control('products.'.$key.'.id', ['type' => 'checkbox', 'hiddenField' => false, 'value' => $product->id, 'label'=>false]) ?></td>
                            <td><?= h($product->name) ?></td>
                            <td><?= h($product->quantity) ?></td>
                            <td><?= $this->Form->control('products.'.$key.'._joinData.quantity', ['novalidate'=>true, 'label'=>false]) ?></td>
                    <?php endforeach; ?>
                </table>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
