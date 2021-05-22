<?php

namespace ConsumptionTracker\Core;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

/**
 * Description of Mailer
 *
 * Handles everything concerning mails etc.
 *
 * @author Bert Maurau
 */
class Mailer
{

    // PHPMailer
    private $mail;
    // boolean
    private $loadedTemplate;
    // string
    private $templateSubject;
    // string
    private $templateBody;
    // string
    private $templateAltBody;
    private $fromName;

    public function __construct()
    {
        $isDebug = (Config::getInstance() -> API() -> env === 'dev');

        $this -> setFromName('Consumption Tracker');

        $this -> mail = new PHPMailer($isDebug);
        $this -> mail -> CharSet = 'UTF-8';
        $this -> mail -> Encoding = 'base64';

        $this -> mail -> SMTPDebug = 0;
        $this -> mail -> isSMTP();
        $this -> mail -> Host = Config::getInstance() -> SMTP() -> host;
        $this -> mail -> SMTPAuth = true;
        $this -> mail -> Username = Config::getInstance() -> SMTP() -> user;
        $this -> mail -> Password = Config::getInstance() -> SMTP() -> pass;
        $this -> mail -> SMTPSecure = Config::getInstance() -> SMTP() -> secure;
        $this -> mail -> Port = Config::getInstance() -> SMTP() -> port;
    }

    /**
     * Add an attachment
     *
     * @param string $filename
     *
     * @return $this
     */
    public function addAttachment($filename)
    {
        $this -> mail -> addAttachment($filename);
        return $this;
    }

    /**
     * Load requested template data
     *
     * @param string $template
     *
     * @return string
     */
    public function build(string $template, string $title, array $placeholders = [])
    {
        $template = file_get_contents(Config::getInstance() -> Paths() -> mailTemplates . $template . '.html');

        // add the logo, always
        $placeholders['URL_LOGO'] = Config::getInstance() -> API() -> webApp . '/assets/icons/icon-128x128.png';

        foreach ($placeholders as $placeholder => $value) {
            $template = str_replace("{{" . $placeholder . "}}", $value, $template);
            $template = str_replace("{{ " . $placeholder . " }}", $value, $template);
        }

        $this -> setTemplateBody($template);
        $this -> setTemplateSubject($title);

        $this -> setLoadedTemplate(true);

        return $this;
    }

    /**
     * Send loaded Template as mail
     *
     * @param string $toEmail
     * @param string $fromEmail
     * @param boolean $bccSu
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function send($toEmail, $toName = null, $fromEmail = null)
    {

        if (!$this -> loadedTemplate) {
            throw new \Exception("No mailer template loaded!");
        }

        $fromName = $this -> getFromName() ?: 'Consumption Tracker';

        try {
            $this -> mail -> setFrom(($fromEmail ?: Config::getInstance() -> SMTP() -> user), $fromName);
            $this -> mail -> addAddress($toEmail, $toName);

            $this -> mail -> isHTML(true);
            $this -> mail -> Subject = $this -> getTemplateSubject();
            $this -> mail -> Body = $this -> getTemplateBody();
            $this -> mail -> AltBody = $this -> getTemplateAltBody();

            return $this -> mail -> send();
        } catch (\Exception $e) {
            throw new \Exception('Message could not be sent. Mailer Error: ' . $this -> mail -> ErrorInfo);
        }
    }

    /**
     * Get loadedTemplate
     *
     * @return boolean
     */
    public function getLoadedTemplate()
    {
        return $this -> loadedTemplate;
    }

    /**
     * Get tempalteSubject
     *
     * @return string
     */
    public function getTemplateSubject()
    {
        return $this -> templateSubject;
    }

    /**
     * Get templateBody
     *
     * @return string
     */
    public function getTemplateBody()
    {
        return $this -> templateBody;
    }

    /**
     * Get templateAltBody
     *
     * @return string
     */
    public function getTemplateAltBody()
    {
        return $this -> templateAltBody;
    }

    /**
     * Set loadedTemplate
     *
     * @param type $loadedTemplate
     *
     * @return $this
     */
    public function setLoadedTemplate($loadedTemplate)
    {
        $this -> loadedTemplate = (boolean) $loadedTemplate;
        return $this;
    }

    /**
     * Set templateSubject
     *
     * @param string $templateSubject
     *
     * @return $this
     */
    public function setTemplateSubject($templateSubject)
    {
        $this -> templateSubject = (string) $templateSubject;
        return $this;
    }

    /**
     * Set templateBody
     *
     * @param string $templateBody
     *
     * @return $this
     */
    public function setTemplateBody($templateBody)
    {
        $this -> templateBody = (string) $templateBody;
        return $this;
    }

    /**
     * Set templateAltBody
     *
     * @param string $templateAltBody
     *
     * @return $this
     */
    public function setTemplateAltBody($templateAltBody)
    {
        $this -> templateAltBody = (string) $templateAltBody;
        return $this;
    }

    /**
     * Get From Name
     *
     * @return string
     */
    public function getFromName()
    {
        return $this -> fromName;
    }

    /**
     * Set From Name
     *
     * @param string $fromName
     *
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this -> fromName = (string) $fromName;
        return $this;
    }

}
