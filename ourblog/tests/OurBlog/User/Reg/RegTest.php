<?php
/**
 * @group reg
 */
class OurBlog_User_RegTest extends OurBlog_DatabaseTestCase
{
    protected $data;

    public function getDataSet()
    {
        $this->data = array(
            'email'           => 'joe@ourats.com',
            'username'        => 'Joe',
            'password'        => '654321',
            'confirmPassword' => '654321'
        );
    
        return $this->createArrayDataSet(
            include __DIR__ . '/fixtures.php'
        );
    }
    
    public function testEmailKeyIsRequired()
    {
        unset($this->data['email']);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required key email');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testUsernameKeyIsRequired()
    {
        unset($this->data['username']);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required key username');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testPasswordKeyIsRequired()
    {
        unset($this->data['password']);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required key password');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testConfirmPasswordKeyIsRequired()
    {
        unset($this->data['confirmPassword']);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required key confirmPassword');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testEmailIsRequired()
    {
        $this->data['email'] = '';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email required');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testUsernameIsRequired()
    {
        $this->data['username'] = '';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('username required');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testPasswordIsRequired()
    {
        $this->data['password'] = '';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('password required');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testConfirmPasswordIsRequired()
    {
        $this->data['confirmPassword'] = '';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('confirmPassword required');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testEmailTooShort()
    {
        $this->data['email'] = 'a@bc';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email too short, minlength is 5');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testEmailTooLong()
    {
        $this->data['email'] = str_pad('joe@ourats.com', 201, 'a', STR_PAD_LEFT);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email too long, maxlength is 200');
        
        OurBlog_User::reg($this->data);
    }
    
    public function invalidEmailFormats()
    {
        return array(
            array('Iamjoe'),
            array('<script>alert("joe")</script>@qq.com'),
            array('joe@####'),
            array('joe@ourats')
        );
    }
    
    /**
     * @dataProvider invalidEmailFormats
     */
    public function testEmailFormat($email)
    {
        $this->data['email'] = $email;
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid email');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testUsernameMaxLength()
    {
        $this->data['username'] = 'joe' . str_repeat('何', 28); // mb_strlen = 31
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('username too long, maxlength is 30');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testPasswordTooShort()
    {
        $this->data['password'] = '123';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid password, length limit 6 ~ 50');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testPasswordTooLong()
    {
        $this->data['password'] = str_pad('123456', 51, 'a');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid password, length limit 6 ~ 50');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testConfirmPasswordShouldEqualToPassword()
    {
        $this->data['confirmPassword'] = '123';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('confirmPassword should equal to password');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testRegisteredEmailCannotReg()
    {
        $this->data['email'] = 'bob@ourats.com';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email already registered');
        
        OurBlog_User::reg($this->data);
    }
    
    public function testReg()
    {
        OurBlog_User::reg($this->data);
        
        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');

        $dataSet = $this->getConnection()->createDataSet(array('user'));
        $filterDataSet = new PHPUnit_DbUnit_DataSet_FilterDataSet($dataSet);
        $filterDataSet->setExcludeColumnsForTable('user', array('create_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
