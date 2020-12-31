<?php

namespace App\Observers;

use App\Jobs\SyncModelJob;
use Bschmitt\Amqp\Message;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpAmqpLib\Exception\AMQPIOException;

class SyncModelObserver
{
    public function created(Model $model)
    {
        $modelName = $this->getModelName($model);
        $modelClass = $this->getModel($model);
        $data = $model->toArray();
        $action = __FUNCTION__;
        $routingKey = "model.{$modelName}.{$action}";
        $id = $model->id;

        try {
            $this->publish($routingKey, $data);
        } catch (AMQPIOException $e) {
            $this->reportException([
                'modelClass' => $modelClass,
                'modelName' => $modelName,
                'id' => $id,
                'exception' => $e,
                'action' => $action,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function updated(Model $model)
    {
        $modelName = $this->getModelName($model);
        $modelClass = $this->getModel($model);
        $id = $model->id;
        $data = $model->toArray();
        $action = __FUNCTION__;
        $routingKey = "model.{$modelName}.{$action}";
        try {
            $this->publish($routingKey, $data);
        } catch (AMQPIOException $e) {
            $this->reportException([
                'modelClass' => $modelClass,
                'modelName' => $modelName,
                'id' => $id,
                'exception' => $e,
                'action' => $action,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function deleted(Model $model)
    {
        $modelName = $this->getModelName($model);
        $modelClass = $this->getModel($model);
        $data = ['id' => $model->id];
        $id = $model->id;
        $action = __FUNCTION__;
        $routingKey = "model.{$modelName}.{$action}";
        try {
            $this->publish($routingKey, $data);
        } catch (AMQPIOException $e) {
            $this->reportException([
                'modelClass' => $modelClass,
                'modelName' => $modelName,
                'id' => $id,
                'exception' => $e,
                'action' => $action,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }

    protected function getModelName(Model $model)
    {
        $shortName = (new \ReflectionClass($model))->getShortName();
        return Str::snake($shortName);
    }

    protected function getModel(Model $model)
    {
        return (new \ReflectionClass($model))->getName();
    }

    protected function publish(string $routingKey, array $data)
    {
        dispatch(new SyncModelJob($routingKey, $data));
    }

    protected function reportException($params)
    {
        list(
            'modelClass' => $modelClass,
            'modelName' => $modelName,
            'id' => $id,
            'exception' => $exception,
            'action' => $action,
            ) = $params;
        $myException = new Exception("The model {$modelName} with ID {$id} not synced on {$action} and class {$modelClass}", 0, $exception);
        report($myException);
    }
}
