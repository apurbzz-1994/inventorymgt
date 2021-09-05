<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $orders = $this->paginate($this->Orders);

        $this->set(compact('orders'));
    }

    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // this is an entity object
        $order = $this->Orders->get($id, [
            'contain' => ['Products'],
        ]);

        $orderProducts = $order->products;
        $total = 0;

        foreach($orderProducts as $p){
            $total += (float) $p->_joinData->subtotal;
        }

        strval($total);

        $this->set(compact('order','total'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $order = $this->Orders->newEmptyEntity();
        if ($this->request->is('post')) {
            
            //fetching request array
            $requestData = $this->request->getData();

             //debug($requestData);
             //exit;

            foreach($requestData['products'] as $key => $data):
            
            // check to see if any of the arrays have null ids (have not been selected):
               if(empty($data['id'])){
                   // if found, remove from data array
                   unset($requestData['products'][$key]);
               }     
            endforeach;

           $productsWithSubtotal = $requestData['products'];


            //iterating through the $requestData array to calculate subtotal for each
            //product
            foreach($requestData['products'] as $key=>$p){
                $fetchedProduct = $this->Orders->Products->get($p['id']);
                //fetching the price
                $productPrice = (float) $fetchedProduct->sale_price;
                //product quantity
                $productQty = (float) $p['_joinData']['quantity'];
                //calculating the subtotal
                $productSubtotal = strval($productPrice * $productQty);
                //inserting the subtotal to joindata
                $productsWithSubtotal[$key]['_joinData']['subtotal'] = $productSubtotal;

                // updating the quantity of product in stock (requires validation) 
                // validation 1 - checking to see if current stock is zero
                // validation 2 - checking to see if productQty > currentStock
                $currentStock = $fetchedProduct->quantity;
                $newQuantity = (int) $currentStock - $productQty;
                $productsWithSubtotal[$key]['quantity'] = (int) $newQuantity;
            }

            // replacing the products array in $requestData with updated array
            // this requires validation checks
            $requestData['products'] = $productsWithSubtotal;

            //debug($productsWithSubtotal);
            //exit;


            $order = $this->Orders->patchEntity($order, $requestData);
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $products = $this->Orders->Products->find('all');
        $this->set(compact('order', 'products'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $order = $this->Orders->get($id, [
            'contain' => ['Products'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $products = $this->Orders->Products->find('list', ['limit' => 200]);
        $this->set(compact('order', 'products'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The order has been deleted.'));
        } else {
            $this->Flash->error(__('The order could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
