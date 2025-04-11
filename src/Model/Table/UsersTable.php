<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Plans');
        $this->hasMany('Hobbies');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Authentication.Identity');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Name is required')
            ->maxLength('name', 100, 'Name must be less than 100 characters');

        $validator
            ->notEmptyString('email', 'Email is required')
            ->email('email', false, 'Please provide a valid email')
            ->maxLength('email', 100, 'Email must be less than 100 characters');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->allowEmptyFile('profile_picture')
            ->add('profile_picture', [
                'validExtension' => [
                    'rule' => ['extension', ['jpeg', 'jpg', 'png']],
                    'message' => __('Only JPEG or PNG images are allowed.')
                ],
                'validMimeType' => [
                    'rule' => function ($value, $context) {
                        if (is_string($value)) {
                            return true; // Already validated existing file
                        }
                        if (!$value || $value->getError() !== UPLOAD_ERR_OK) {
                            return true; // Skip validation if no file
                        }
                        
                        $validMimeTypes = ['image/jpeg', 'image/png'];
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $value->getStream()->getMetadata('uri'));
                        finfo_close($finfo);
                        
                        return in_array($mime, $validMimeTypes);
                    },
                    'message' => __('Invalid file type. Only JPEG or PNG allowed.')
                ],
                'validSize' => [
                    'rule' => function ($value, $context) {
                        if (is_string($value) || !$value || $value->getError() !== UPLOAD_ERR_OK) {
                            return true; // Skip validation
                        }
                        return $value->getSize() <= 1024 * 1024; // 1MB
                    },
                    'message' => __('File must be less than 1MB.')
                ]
            ]);
        return $validator;
    }

    public function validationRegister(Validator $validator): Validator
    {
        $validator = $this->validationDefault($validator);
        
        // Add additional registration-specific rules
        $validator
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already registered'
            ])
            ->add('password', 'complexity', [
                'rule' => function ($value, $context) {
                    return (bool)preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $value);
                },
                'message' => 'Password must contain at least 1 uppercase, 1 lowercase, and 1 number'
            ]);
            
        // Hobbies validation
        $validator
            ->requirePresence('hobbies', 'create', 'At least one hobby is required')
            ->notEmptyArray('hobbies', 'At least one hobby is required');
            
        return $validator;
    }
}