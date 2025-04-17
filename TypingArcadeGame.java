import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.input.KeyCode;
import javafx.scene.layout.VBox;
import javafx.scene.paint.Color;
import javafx.scene.text.Font;
import javafx.stage.Stage;
import javafx.animation.AnimationTimer;

public class TypingArcadeGame extends Application {

    private Label sequenceLabel;
    private Label timerLabel;
    private Label scoreLabel;
    private String currentSequence = "CTRL+SHIFT+A";
    private String inputSequence = "";
    private int score = 0;
    private long startTime;
    private final long timeLimit = 5000; // 5 seconds

    @Override
    public void start(Stage primaryStage) {
        // Initialize Labels
        sequenceLabel = new Label("Sequence: " + currentSequence);
        sequenceLabel.setFont(Font.font("Monospaced", 24));
        sequenceLabel.setTextFill(Color.NEON);

        timerLabel = new Label("Time: 5.00 seconds");
        timerLabel.setFont(Font.font("Monospaced", 18));
        timerLabel.setTextFill(Color.RED);

        scoreLabel = new Label("Score: 0");
        scoreLabel.setFont(Font.font("Monospaced", 18));
        scoreLabel.setTextFill(Color.YELLOW);

        VBox root = new VBox(20, sequenceLabel, timerLabel, scoreLabel);
        root.setStyle("-fx-background-color: black; -fx-padding: 20px;");

        Scene scene = new Scene(root, 400, 300);

        // KeyPress Handling
        scene.setOnKeyPressed(event -> handleKeyPress(event.getCode()));

        // Start Timer
        startTime = System.nanoTime();
        AnimationTimer timer = new AnimationTimer() {
            @Override
            public void handle(long now) {
                updateTimer(now);
            }
        };
        timer.start();

        // Stage Configuration
        primaryStage.setTitle("Typing Arcade Game");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    private void handleKeyPress(KeyCode keyCode) {
        // Translate KeyCode to String (e.g., CTRL -> "CTRL")
        String key = keyCode.toString();

        // Append Key to Input Sequence
        inputSequence += key + "+";

        // Check if the Input Sequence Matches the Current Sequence
        if (inputSequence.equals(currentSequence + "+")) {
            score += 10;
            scoreLabel.setText("Score: " + score);
            generateNewSequence();
        } else if (!currentSequence.startsWith(inputSequence)) {
            // If Input is Incorrect, Reset Sequence
            inputSequence = "";
        }
    }

    private void generateNewSequence() {
        // For simplicity, alternate between predefined sequences
        if (currentSequence.equals("CTRL+SHIFT+A")) {
            currentSequence = "ALT+TAB";
        } else {
            currentSequence = "CTRL+SHIFT+A";
        }

        sequenceLabel.setText("Sequence: " + currentSequence);
        inputSequence = ""; // Reset Input
        startTime = System.nanoTime(); // Reset Timer
    }

    private void updateTimer(long now) {
        long elapsedTime = (now - startTime) / 1_000_000; // Convert to milliseconds
        long remainingTime = timeLimit - elapsedTime;

        if (remainingTime > 0) {
            timerLabel.setText(String.format("Time: %.2f seconds", remainingTime / 1000.0));
        } else {
            timerLabel.setText("Time: 0.00 seconds");
            generateNewSequence(); // Reset on timeout
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
