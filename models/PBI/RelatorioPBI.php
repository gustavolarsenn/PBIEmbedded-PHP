<?php



require_once __DIR__ . '\\..\\..\\config.php';
require_once CAMINHO_BASE . '\\models\\Azure\\AzureAPI.php';
require_once CAMINHO_BASE . '\\models\\Azure\\Capacidade.php';

require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';

require_once CAMINHO_BASE . '\\SessionManager.php';

require_once CAMINHO_BASE . '\\vendor\\autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class RelatorioPBI {
    private $pdo;
    private const LOG_FILE = 'PowerBI';
    private const LOG = 'relatorio_pbi';
    private const CAMINHO_LOG = CAMINHO_BASE . '\\logs\\' . self::LOG_FILE . '.log';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function pegarRelatoriosAtivos(){
        $sql = "SELECT * FROM relatorio_pbi WHERE ativo = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $reportsArray = array_reduce($reports, function($carry, $report) {
        $carry[$report['relatorio_clean']] = [
            "relatorio" => $report['relatorio'],
            "id_relatorio" => $report['id_relatorio'],
            "id_dataset" => $report['id_dataset'],
            "rls" => $report['rls']
        ];
            return $carry;
        }, []);

        return $reportsArray;
    }

    function gerarRelatorioPBI($actualLink){

        $capacidade = new Capacidade();
    
        $log = new Logger(self::LOG);
        $log->pushHandler(new StreamHandler(self::CAMINHO_LOG, Logger::DEBUG));
    
        SessionManager::checarSessao();
    
        $log->info('Gerando relatório PowerBI', ['user' => $_SESSION['id_usuario']]);
        try {
            $capacidade->criarTarefaChecarCapacidade();
            $conn = (new Database())->getConnection();
            
            $reports = self::pegarRelatoriosAtivos();
            
            if (!isset($reports[$actualLink])) {
                $log->error('Relatório não encontrado', ['user' => $_SESSION['id_usuario'], ['relatorio' => $actualLink]]);            
                header('Location: /views/index.php');
                exit;
            } 
    
            $currentReport = $reports[$actualLink];
            
            $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
            $powerBISession->criarSessaoPBI();
            
            $azureAPI = new AzureAPI();
    
            $capacidadeAtiva = $capacidade->ligarCapacity($powerBISession->sessoesAtivasPBI(), $azureAPI);
    
            if (!json_decode($capacidadeAtiva)->sucesso){
                $log->error('Não foi possível gerar relatório, capacidade não iniciada', ['user' => $_SESSION['id_usuario'], 'relatorio' => $actualLink]);
                return $capacidadeAtiva;
            }
    
            $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['id_relatorio'], $currentReport['id_dataset'], null);
            $conn = null;
    
            $log->info('Relatório gerado com sucesso', ['user' => $_SESSION['id_usuario'], 'relatorio' => $actualLink]);
            return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);
    
        } catch (Exception $e) {
            $log->error('Erro ao gerar relatório PowerBI: ' . $e->getMessage(), ['user' => $_SESSION['id_usuario'], 'relatorio' => $actualLink]);
        }
    }
}